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
        $history = $db->table('shipments')->where('courier_id', $courier['id'])->where('status', 'DELIVERED')->orderBy('delivered_at', 'DESC')->limit(10)->get()->getResultArray();
        $earningsToday = (int) ($db->table('shipments')->selectSum('courier_earning')->where('courier_id', $courier['id'])->where('status', 'DELIVERED')->where('DATE(delivered_at)', date('Y-m-d'))->get()->getRowArray()['courier_earning'] ?? 0);

        return view('courier/dashboard', [
            'title' => 'Courier Dashboard',
            'courier' => $courier,
            'available' => $available,
            'active' => $active,
            'completedToday' => $completedToday,
            'history' => $history,
            'earningsToday' => $earningsToday,
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

        public function toggleStatus()
        {
            $db = \Config\Database::connect();
            $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
            if (!$courier) {
                return redirect()->to('/courier/dashboard')->with('error', 'Courier tidak ditemukan');
            }
            $status = strtoupper((string) $this->request->getPost('status'));
            if (!in_array($status, ['OFFLINE', 'ONLINE', 'AVAILABLE'], true)) {
                return redirect()->to('/courier/dashboard')->with('error', 'Status tidak valid');
            }
            $db->table('couriers')->where('id', $courier['id'])->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
            return redirect()->to('/courier/dashboard')->with('success', 'Status courier diperbarui');
        }

        public function pickup(int $shipmentId)
        {
            return $this->changeShipmentStatus($shipmentId, 'ASSIGNED', 'PICKED_UP', ['picked_up_at' => date('Y-m-d H:i:s')], 'Pickup berhasil diproses');
        }

        public function onDelivery(int $shipmentId)
        {
            return $this->changeShipmentStatus($shipmentId, 'PICKED_UP', 'ON_DELIVERY', [], 'Status pengiriman diperbarui');
        }

        public function complete(int $shipmentId)
        {
            $db = \Config\Database::connect();
            $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
            if (!$courier) {
                return redirect()->to('/courier/dashboard')->with('error', 'Courier tidak ditemukan');
            }

            $shipment = $db->table('shipments')->where('id', $shipmentId)->where('courier_id', $courier['id'])->get()->getRowArray();
            if (!$shipment || $shipment['status'] !== 'ON_DELIVERY') {
                return redirect()->to('/courier/dashboard')->with('error', 'Shipment tidak valid untuk diselesaikan');
            }

            $otp = trim((string) $this->request->getPost('otp_code'));
            if ($otp === '' || $otp !== (string) $shipment['otp_code']) {
                return redirect()->to('/courier/dashboard')->with('error', 'OTP tidak valid');
            }

            $proofPath = $shipment['proof_image'] ?? null;
            $proof = $this->request->getFile('proof_image');
            if ($proof && $proof->isValid() && $proof->getError() !== UPLOAD_ERR_NO_FILE) {
                if (!in_array($proof->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    return redirect()->to('/courier/dashboard')->with('error', 'Bukti kirim harus gambar');
                }
                $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'shipments';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0775, true);
                }
                $proofName = $proof->getRandomName();
                $proof->move($targetDir, $proofName, true);
                $proofPath = 'uploads/shipments/' . $proofName;
            }

            $db->table('shipments')->where('id', $shipmentId)->update([
                'status' => 'DELIVERED',
                'otp_verified_at' => date('Y-m-d H:i:s'),
                'proof_image' => $proofPath,
                'delivered_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'courier_earning' => (int)($shipment['courier_earning'] ?: floor(((int) $shipment['delivery_fee']) * 0.7)),
            ]);
            $db->table('orders')->where('id', (int) $shipment['order_id'])->update(['status' => 'COMPLETED', 'completed_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]);
            $db->table('couriers')->where('id', $courier['id'])->set('total_deliveries', 'total_deliveries + 1', false)->set('total_earnings', 'total_earnings + ' . (int)($shipment['courier_earning'] ?: floor(((int) $shipment['delivery_fee']) * 0.7)), false)->update();

            return redirect()->to('/courier/dashboard')->with('success', 'Pengantaran selesai');
        }

        private function changeShipmentStatus(int $shipmentId, string $expected, string $next, array $extra, string $message)
        {
            $db = \Config\Database::connect();
            $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
            if (!$courier) {
                return redirect()->to('/courier/dashboard')->with('error', 'Courier tidak ditemukan');
            }

            $shipment = $db->table('shipments')->where('id', $shipmentId)->where('courier_id', $courier['id'])->get()->getRowArray();
            if (!$shipment || $shipment['status'] !== $expected) {
                return redirect()->to('/courier/dashboard')->with('error', 'Transisi status shipment tidak valid');
            }

            $payload = array_merge($extra, ['status' => $next, 'updated_at' => date('Y-m-d H:i:s')]);
            $db->table('shipments')->where('id', $shipmentId)->update($payload);

            return redirect()->to('/courier/dashboard')->with('success', $message);
        }
    }
}
