<?php

namespace App\Services;

class AdminAnalyticsService
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function parseRange(?string $preset, ?string $from, ?string $to): array
    {
        $today = date('Y-m-d');
        switch ($preset) {
            case 'yesterday':
                return ['from' => date('Y-m-d', strtotime('-1 day')), 'to' => date('Y-m-d', strtotime('-1 day'))];
            case 'last7':
                return ['from' => date('Y-m-d', strtotime('-6 day')), 'to' => $today];
            case 'last30':
                return ['from' => date('Y-m-d', strtotime('-29 day')), 'to' => $today];
            case 'this_month':
                return ['from' => date('Y-m-01'), 'to' => $today];
            case 'last_month':
                return ['from' => date('Y-m-01', strtotime('first day of last month')), 'to' => date('Y-m-t', strtotime('last month'))];
            case 'custom':
                return ['from' => $from ?: $today, 'to' => $to ?: $today];
            case 'today':
            default:
                return ['from' => $today, 'to' => $today];
        }
    }

    public function kpis(?string $from = null, ?string $to = null): array
    {
        $ordersQuery = $this->db->table('orders')
            ->select("COALESCE(SUM(total),0) as gmv, COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'PENDING_PAYMENT' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status IN ('COMPLETED', 'FEEDBACK') THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = 'CANCELLED' THEN 1 ELSE 0 END) as cancelled_orders", false);

        if ($from && $to) {
            $ordersQuery->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to);
        }
        $orderAgg = $ordersQuery->get()->getRowArray() ?: [];

        $paidPayments = $this->db->table('payments')->select('COALESCE(SUM(amount),0) as revenue', false)->where('status', 'PAID');
        if ($from && $to) {
            $paidPayments->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to);
        }
        $revenueRow = $paidPayments->get()->getRowArray();

        $totalCustomers = $this->countByRole('customer');
        $activeCustomers = $this->activeCustomers($from, $to);
        $totalUmkm = $this->countByRole('umkm');
        $verifiedUmkm = $this->db->table('umkms')->where('verification_status', 'VERIFIED')->countAllResults();
        $pendingUmkm = $this->db->table('umkms')->where('verification_status', 'PENDING')->countAllResults();

        $totalCouriers = $this->countByRole('courier');
        $activeCouriers = $this->db->table('couriers')->whereIn('status', ['ONLINE', 'AVAILABLE', 'ASSIGNED', 'PICKUP', 'ON_DELIVERY'])->countAllResults();
        $pendingCouriers = $this->db->table('couriers')->where('verification_status', 'PENDING')->countAllResults();

        return [
            'gmv' => (int) ($orderAgg['gmv'] ?? 0),
            'revenue' => (int) ($revenueRow['revenue'] ?? 0),
            'total_orders' => (int) ($orderAgg['total_orders'] ?? 0),
            'pending_orders' => (int) ($orderAgg['pending_orders'] ?? 0),
            'completed_orders' => (int) ($orderAgg['completed_orders'] ?? 0),
            'cancelled_orders' => (int) ($orderAgg['cancelled_orders'] ?? 0),
            'total_customers' => $totalCustomers,
            'active_customers' => $activeCustomers,
            'total_umkm' => $totalUmkm,
            'verified_umkm' => $verifiedUmkm,
            'pending_umkm' => $pendingUmkm,
            'total_products' => $this->db->table('products')->countAllResults(),
            'active_products' => $this->db->table('products')->where('status', 'ACTIVE')->countAllResults(),
            'total_couriers' => $totalCouriers,
            'active_couriers' => $activeCouriers,
            'pending_couriers' => $pendingCouriers,
            'shipments' => $this->db->table('shipments')->countAllResults(),
            'pending_moderation' => $this->db->table('products')->where('status', 'PENDING')->countAllResults(),
            'pending_feedback' => $this->db->table('feedbacks')->where('status', 'PENDING')->countAllResults(),
        ];
    }

    public function ordersOverTime(int $days = 14): array
    {
        $fromDate = date('Y-m-d', strtotime('-' . max(0, $days - 1) . ' days'));

        return $this->db->table('orders')
            ->select('DATE(created_at) as d, COUNT(*) as total, COALESCE(SUM(total),0) as revenue', false)
            ->where('DATE(created_at) >=', $fromDate)
            ->groupBy('DATE(created_at)')
            ->orderBy('d', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function statusDistribution(): array
    {
        return $this->db->query('SELECT status, COUNT(*) as total FROM orders GROUP BY status ORDER BY total DESC')->getResultArray();
    }

    public function topProducts(int $limit = 10): array
    {
        return $this->db->query(
            'SELECT p.id, p.name, p.sold_count, p.price, s.name as store_name FROM products p JOIN stores s ON s.id = p.store_id ORDER BY p.sold_count DESC, p.id DESC LIMIT ' . (int) $limit
        )->getResultArray();
    }

    public function topStores(int $limit = 10): array
    {
        return $this->db->query(
            'SELECT s.id, s.name, COALESCE(SUM(oi.subtotal),0) as sales FROM stores s LEFT JOIN order_items oi ON oi.store_id = s.id GROUP BY s.id, s.name ORDER BY sales DESC LIMIT ' . (int) $limit
        )->getResultArray();
    }

    private function countByRole(string $role): int
    {
        return $this->db->table('user_roles ur')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('r.slug', $role)
            ->countAllResults();
    }

    private function activeCustomers(?string $from, ?string $to): int
    {
        $builder = $this->db->table('orders')->select('COUNT(DISTINCT customer_id) as total', false);
        if ($from && $to) {
            $builder->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to);
        }
        $row = $builder->get()->getRowArray();

        return (int) ($row['total'] ?? 0);
    }
}
