<?php

namespace App\Controllers\Courier;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $roles = session()->get('roles') ?? [];
        if (!in_array('courier', $roles, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db = \Config\Database::connect();
        $userId = (int) session()->get('user_id');
        $courier = $db->table('couriers')->where('user_id', $userId)->get()->getRowArray();

        if (!$courier) {
            return redirect()->to('/')->with('error', 'Data courier tidak ditemukan');
        }

        $available = $db->table('shipments')->where('status', 'READY_FOR_PICKUP')->where('courier_id IS NULL', null, false)->orderBy('created_at', 'ASC')->limit(10)->get()->getResultArray();
        $active = $db->table('shipments')->where('courier_id', $courier['id'])->whereIn('status', ['ASSIGNED', 'PICKED_UP', 'ON_DELIVERY'])->orderBy('updated_at', 'DESC')->get()->getResultArray();
        $completedToday = $db->table('shipments')->where('courier_id', $courier['id'])->where('status', 'DELIVERED')->where('DATE(delivered_at)', date('Y-m-d'))->countAllResults();

        return view('courier/dashboard', [
            'title' => 'Courier Dashboard',
            'courier' => $courier,
            'available' => $available,
            'active' => $active,
            'completedToday' => $completedToday,
        ]);
    }

    public function accept(int $shipmentId)
    {
        $roles = session()->get('roles') ?? [];
        if (!in_array('courier', $roles, true)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db = \Config\Database::connect();
        $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
        if (!$courier) {
            return redirect()->to('/courier/dashboard')->with('error', 'Courier tidak ditemukan');
        }

        $db->transBegin();
        try {
            $shipment = $db->query('SELECT * FROM shipments WHERE id = ? FOR UPDATE', [$shipmentId])->getRowArray();
            if (!$shipment || $shipment['status'] !== 'READY_FOR_PICKUP' || !empty($shipment['courier_id'])) {
                $db->transRollback();
                return redirect()->to('/courier/dashboard')->with('error', 'Shipment sudah diambil courier lain atau tidak tersedia');
            }

            $db->table('shipments')->where('id', $shipmentId)->update([
                'status' => 'ASSIGNED',
                'courier_id' => $courier['id'],
                'assigned_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            if ($db->transStatus() === false) {
                $db->transRollback();
                return redirect()->to('/courier/dashboard')->with('error', 'Gagal menerima shipment');
            }

            $db->transCommit();
            return redirect()->to('/courier/dashboard')->with('success', 'Shipment berhasil diterima');
        } catch (\Throwable $e) {
            $db->transRollback();
            return redirect()->to('/courier/dashboard')->with('error', 'Gagal menerima shipment');
        }
    }
}
