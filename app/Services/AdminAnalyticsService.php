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
        $orders = $this->db->table('orders');
        if ($from && $to) {
            $orders->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to);
        }
        $orderRows = $orders->get()->getResultArray();

        $gmv = 0;
        $pending = 0;
        $completed = 0;
        $cancelled = 0;
        foreach ($orderRows as $row) {
            $gmv += (int) $row['total'];
            if ($row['status'] === 'PENDING_PAYMENT') {
                $pending++;
            }
            if (in_array($row['status'], ['COMPLETED', 'FEEDBACK'], true)) {
                $completed++;
            }
            if ($row['status'] === 'CANCELLED') {
                $cancelled++;
            }
        }

        $paidPayments = $this->db->table('payments')->selectSum('amount')->where('status', 'PAID');
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
            'gmv' => $gmv,
            'revenue' => (int) ($revenueRow['amount'] ?? 0),
            'total_orders' => count($orderRows),
            'pending_orders' => $pending,
            'completed_orders' => $completed,
            'cancelled_orders' => $cancelled,
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
        $rows = $this->db->query(
            'SELECT DATE(created_at) as d, COUNT(*) as total, COALESCE(SUM(total),0) as revenue FROM orders WHERE created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY d ASC',
            [$days]
        )->getResultArray();
        return $rows;
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
        $q = $this->db->table('orders')->select('customer_id')->groupBy('customer_id');
        if ($from && $to) {
            $q->where('DATE(created_at) >=', $from)->where('DATE(created_at) <=', $to);
        }

        return count($q->get()->getResultArray());
    }
}
