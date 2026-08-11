<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;

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
            ->select('oi.*, s.name as store_name')
            ->join('stores s', 's.id = oi.store_id')
            ->where('oi.order_id', $id)->get()->getResultArray();
        $payment = $db->table('payments')->where('order_id', $id)->get()->getRowArray();
        if ($payment && !empty($payment['metadata'])) {
            $payment['meta'] = json_decode((string) $payment['metadata'], true) ?: [];
        }
        $shipments = $db->table('shipments')->where('order_id', $id)->get()->getResultArray();
        return view('customer/orders/show', compact('order', 'items', 'payment', 'shipments'));
    }
}
