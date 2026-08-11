<?php

namespace App\Controllers\Umkm;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $roles = session()->get('roles') ?? [];
        if (!in_array('umkm', $roles, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db = \Config\Database::connect();
        $userId = (int) session()->get('user_id');

        $umkm = $db->table('umkms')->where('user_id', $userId)->get()->getRowArray();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $storeIds = array_column($db->table('stores')->select('id')->where('umkm_id', $umkm['id'])->get()->getResultArray(), 'id');
        $today = date('Y-m-d');

        $salesToday = 0;
        $salesMonth = 0;
        $orders = [];
        if (!empty($storeIds)) {
            $salesToday = (int) ($db->table('order_items')->selectSum('subtotal')->whereIn('store_id', $storeIds)->where('DATE(created_at)', $today)->get()->getRowArray()['subtotal'] ?? 0);
            $salesMonth = (int) ($db->table('order_items')->selectSum('subtotal')->whereIn('store_id', $storeIds)->where('DATE(created_at) >=', date('Y-m-01'))->where('DATE(created_at) <=', $today)->get()->getRowArray()['subtotal'] ?? 0);
            $orders = $db->table('order_items oi')
                ->select('oi.order_id, oi.product_name, oi.quantity, oi.subtotal, o.status, o.created_at')
                ->join('orders o', 'o.id = oi.order_id')
                ->whereIn('oi.store_id', $storeIds)
                ->orderBy('o.created_at', 'DESC')
                ->limit(10)
                ->get()->getResultArray();
        }

        $productsTotal = !empty($storeIds) ? $db->table('products')->whereIn('store_id', $storeIds)->countAllResults() : 0;
        $lowStock = !empty($storeIds) ? $db->table('products')->whereIn('store_id', $storeIds)->where('stock <', 10)->countAllResults() : 0;

        return view('umkm/dashboard', [
            'title' => 'UMKM Dashboard',
            'umkm' => $umkm,
            'salesToday' => $salesToday,
            'salesMonth' => $salesMonth,
            'productsTotal' => $productsTotal,
            'lowStock' => $lowStock,
            'orders' => $orders,
        ]);
    }
}
