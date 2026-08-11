<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Services\ShipmentWorkflowService;

class OrderController extends BaseController
{
    public function index()
    {
        $orders = \Config\Database::connect()->table('orders')
            ->where('customer_id', (int) session()->get('user_id'))
            ->orderBy('created_at', 'DESC')
            ->get()->getResultArray();
        return view('customer/orders/index', ['orders' => $orders]);
    }

    public function show($id)
    {
        $userId = (int) session()->get('user_id');
        $db = \Config\Database::connect();
        $order = $db->table('orders')->where(['id' => $id, 'customer_id' => $userId])->get()->getRowArray();
        if (!$order) {
            return redirect()->to('/orders')->with('error', 'Pesanan tidak ditemukan');
        }
        $items = $db->table('order_items oi')
            ->select("oi.*, s.name as store_name, p.slug as product_slug, (SELECT file_path FROM product_images pi WHERE pi.product_id = oi.product_id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
            ->join('stores s', 's.id = oi.store_id')
            ->join('products p', 'p.id = oi.product_id', 'left')
            ->where('oi.order_id', $id)->get()->getResultArray();
        $payment = $db->table('payments')->where('order_id', $id)->get()->getRowArray();
        if ($payment && !empty($payment['metadata'])) {
            $payment['meta'] = json_decode((string) $payment['metadata'], true) ?: [];
        }
        $workflow = new ShipmentWorkflowService();
        $shipments = $db->table('shipments s')
            ->select('s.*, c.current_lat, c.current_lng, u.name as courier_name, u.phone as courier_phone')
            ->join('couriers c', 'c.id = s.courier_id', 'left')
            ->join('users u', 'u.id = c.user_id', 'left')
            ->where('s.order_id', $id)
            ->get()->getResultArray();
        foreach ($shipments as &$shipment) {
            $shipment['timeline'] = $workflow->buildTimeline($shipment);
            $shipment['latest_tracking'] = $workflow->latestTracking((int) $shipment['id']);
        }
        $reviews = $db->table('feedbacks')->where('order_id', $id)->where('customer_id', $userId)->get()->getResultArray();
        $reviewedIds = array_map(static fn (array $row) => (int) $row['product_id'], $reviews);
        return view('customer/orders/show', compact('order', 'items', 'payment', 'shipments', 'reviews', 'reviewedIds'));
    }

    public function tracking(int $id)
    {
        $userId = (int) session()->get('user_id');
        $db = \Config\Database::connect();
        $order = $db->table('orders')->where(['id' => $id, 'customer_id' => $userId])->get()->getRowArray();
        if (!$order) {
            return redirect()->to('/orders')->with('error', 'Pesanan tidak ditemukan');
        }

        $workflow = new ShipmentWorkflowService();
        $shipments = $db->table('shipments s')
            ->select('s.*, c.current_lat, c.current_lng, u.name as courier_name, u.phone as courier_phone')
            ->join('couriers c', 'c.id = s.courier_id', 'left')
            ->join('users u', 'u.id = c.user_id', 'left')
            ->where('s.order_id', $id)
            ->get()->getResultArray();
        foreach ($shipments as &$shipment) {
            $shipment['timeline'] = $workflow->buildTimeline($shipment);
            $shipment['latest_tracking'] = $workflow->latestTracking((int) $shipment['id']);
        }

        return view('customer/orders/tracking', compact('order', 'shipments'));
    }

    public function review(int $orderId)
    {
        $userId = (int) session()->get('user_id');
        $db = \Config\Database::connect();
        $order = $db->table('orders')->where(['id' => $orderId, 'customer_id' => $userId])->get()->getRowArray();
        if (!$order || !in_array($order['status'], ['COMPLETED', 'FEEDBACK'], true)) {
            return redirect()->to('/orders/' . $orderId)->with('error', 'Review hanya tersedia untuk pesanan selesai');
        }

        $productId = (int) $this->request->getPost('product_id');
        $item = $db->table('order_items')->where(['order_id' => $orderId, 'product_id' => $productId])->get()->getRowArray();
        if (!$item) {
            return redirect()->to('/orders/' . $orderId)->with('error', 'Produk tidak ditemukan pada pesanan');
        }

        $rating = max(1, min(5, (int) $this->request->getPost('rating')));
        $comment = trim((string) $this->request->getPost('comment'));
        $exists = $db->table('feedbacks')->where([
            'order_id' => $orderId,
            'customer_id' => $userId,
            'product_id' => $productId,
        ])->countAllResults();
        if ($exists) {
            return redirect()->to('/orders/' . $orderId)->with('error', 'Review untuk produk ini sudah pernah dikirim');
        }

        $db->table('feedbacks')->insert([
            'order_id' => $orderId,
            'customer_id' => $userId,
            'product_id' => $productId,
            'store_id' => (int) $item['store_id'],
            'rating' => $rating,
            'comment' => $comment,
            'status' => 'APPROVED',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $feedbackAgg = $db->table('feedbacks')
            ->select('COUNT(*) as total, COALESCE(AVG(rating),0) as avg_rating', false)
            ->where('product_id', $productId)
            ->where('status', 'APPROVED')
            ->get()->getRowArray();
        $db->table('products')->where('id', $productId)->update([
            'rating_count' => (int) ($feedbackAgg['total'] ?? 0),
            'rating_avg' => round((float) ($feedbackAgg['avg_rating'] ?? 0), 2),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $storeAgg = $db->table('feedbacks')
            ->select('COUNT(*) as total, COALESCE(AVG(rating),0) as avg_rating', false)
            ->where('store_id', (int) $item['store_id'])
            ->where('status', 'APPROVED')
            ->get()->getRowArray();
        $db->table('stores')->where('id', (int) $item['store_id'])->update([
            'rating_count' => (int) ($storeAgg['total'] ?? 0),
            'rating_avg' => round((float) ($storeAgg['avg_rating'] ?? 0), 2),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/orders/' . $orderId)->with('success', 'Review berhasil dikirim');
    }

    public function reviews()
    {
        $rows = \Config\Database::connect()->table('feedbacks f')
            ->select('f.*, p.name as product_name, p.slug as product_slug, s.name as store_name')
            ->join('products p', 'p.id = f.product_id')
            ->join('stores s', 's.id = f.store_id')
            ->where('f.customer_id', (int) session()->get('user_id'))
            ->orderBy('f.created_at', 'DESC')
            ->get()->getResultArray();

        return view('customer/orders/reviews', ['reviews' => $rows]);
    }
}
