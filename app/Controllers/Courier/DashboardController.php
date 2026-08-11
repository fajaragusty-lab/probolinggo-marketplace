<?php

namespace App\Controllers\Courier;

use App\Controllers\BaseController;
use App\Services\ShipmentWorkflowService;

class DashboardController extends BaseController
{
    private const DEFAULT_COURIER_EARNING_RATIO = 0.7;

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

        $available = $db->table('shipments')
            ->where('status', 'READY_FOR_PICKUP')
            ->where('courier_id IS NULL', null, false)
            ->orderBy('created_at', 'ASC')
            ->limit(10)
            ->get()->getResultArray();

        $active = $db->table('shipments')
            ->where('courier_id', $courier['id'])
            ->whereIn('status', ['ACCEPTED', 'ARRIVED_PICKUP', 'PICKED_UP', 'ON_DELIVERY', 'ARRIVED_DESTINATION', 'OTP_VERIFIED', 'PROOF_UPLOADED'])
            ->orderBy('updated_at', 'DESC')
            ->get()->getResultArray();

        $completedToday = $db->table('shipments')
            ->where('courier_id', $courier['id'])
            ->where('status', 'DELIVERED')
            ->where('DATE(delivered_at)', date('Y-m-d'))
            ->countAllResults();

        $history = $db->table('shipments')
            ->where('courier_id', $courier['id'])
            ->where('status', 'DELIVERED')
            ->orderBy('delivered_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $earningsToday = (int) ($db->table('shipments')
            ->selectSum('courier_earning')
            ->where('courier_id', $courier['id'])
            ->where('status', 'DELIVERED')
            ->where('DATE(delivered_at)', date('Y-m-d'))
            ->get()->getRowArray()['courier_earning'] ?? 0);

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
                'status' => 'ACCEPTED',
                'courier_id' => $courier['id'],
                'assigned_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $db->table('couriers')->where('id', $courier['id'])->update([
                'status' => 'ASSIGNED',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            (new ShipmentWorkflowService())->syncOrderStatus((int) $shipment['order_id']);

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

        $db->table('couriers')->where('id', $courier['id'])->update([
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/courier/dashboard')->with('success', 'Status courier diperbarui');
    }

    public function pickup(int $shipmentId)
    {
        return $this->changeShipmentStatus($shipmentId, 'ARRIVED_PICKUP', 'PICKED_UP', ['picked_up_at' => date('Y-m-d H:i:s')], 'Pickup berhasil diproses', 'PICKUP');
    }

    public function onDelivery(int $shipmentId)
    {
        return $this->changeShipmentStatus($shipmentId, 'PICKED_UP', 'ON_DELIVERY', ['on_delivery_at' => date('Y-m-d H:i:s')], 'Status pengiriman diperbarui', 'ON_DELIVERY');
    }

    public function arrivePickup(int $shipmentId)
    {
        return $this->changeShipmentStatus($shipmentId, 'ACCEPTED', 'ARRIVED_PICKUP', ['arrived_pickup_at' => date('Y-m-d H:i:s')], 'Kurir tiba di titik pickup', 'PICKUP');
    }

    public function arriveDestination(int $shipmentId)
    {
        return $this->changeShipmentStatus($shipmentId, 'ON_DELIVERY', 'ARRIVED_DESTINATION', ['arrived_destination_at' => date('Y-m-d H:i:s')], 'Kurir tiba di alamat tujuan', 'ON_DELIVERY');
    }

    public function verifyOtp(int $shipmentId)
    {
        $db = \Config\Database::connect();
        $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
        if (!$courier) {
            return redirect()->to('/courier/dashboard')->with('error', 'Courier tidak ditemukan');
        }

        $shipment = $db->table('shipments')->where('id', $shipmentId)->where('courier_id', $courier['id'])->get()->getRowArray();
        if (!$shipment || $shipment['status'] !== 'ARRIVED_DESTINATION') {
            return redirect()->to('/courier/dashboard')->with('error', 'Shipment tidak valid untuk verifikasi OTP');
        }

        $otp = trim((string) $this->request->getPost('otp_code'));
        $expectedOtp = (string) ($shipment['otp_code'] ?? '');
        if ($otp === '' || $expectedOtp === '' || !hash_equals($expectedOtp, $otp)) {
            return redirect()->to('/courier/dashboard')->with('error', 'OTP tidak valid');
        }

        $db->table('shipments')->where('id', $shipmentId)->update([
            'status' => 'OTP_VERIFIED',
            'otp_verified_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        (new ShipmentWorkflowService())->syncOrderStatus((int) $shipment['order_id']);

        return redirect()->to('/courier/dashboard')->with('success', 'OTP berhasil diverifikasi');
    }

    public function uploadProof(int $shipmentId)
    {
        $db = \Config\Database::connect();
        $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
        if (!$courier) {
            return redirect()->to('/courier/dashboard')->with('error', 'Courier tidak ditemukan');
        }

        $shipment = $db->table('shipments')->where('id', $shipmentId)->where('courier_id', $courier['id'])->get()->getRowArray();
        if (!$shipment || $shipment['status'] !== 'OTP_VERIFIED') {
            return redirect()->to('/courier/dashboard')->with('error', 'Upload bukti belum dapat dilakukan');
        }

        $proof = $this->request->getFile('proof_image');
        if (!$proof || !$proof->isValid() || $proof->getError() === UPLOAD_ERR_NO_FILE) {
            return redirect()->to('/courier/dashboard')->with('error', 'Bukti kirim wajib diunggah');
        }
        if (!in_array($proof->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return redirect()->to('/courier/dashboard')->with('error', 'Bukti kirim harus gambar');
        }

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'shipments';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $proofName = $proof->getRandomName();
        $proof->move($targetDir, $proofName, true);

        $db->table('shipments')->where('id', $shipmentId)->update([
            'status' => 'PROOF_UPLOADED',
            'proof_image' => 'uploads/shipments/' . $proofName,
            'proof_uploaded_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        (new ShipmentWorkflowService())->syncOrderStatus((int) $shipment['order_id']);

        return redirect()->to('/courier/dashboard')->with('success', 'Bukti pengiriman diunggah');
    }

    public function complete(int $shipmentId)
    {
        $db = \Config\Database::connect();
        $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
        if (!$courier) {
            return redirect()->to('/courier/dashboard')->with('error', 'Courier tidak ditemukan');
        }

        $shipment = $db->table('shipments')->where('id', $shipmentId)->where('courier_id', $courier['id'])->get()->getRowArray();
        if (!$shipment || $shipment['status'] !== 'PROOF_UPLOADED') {
            return redirect()->to('/courier/dashboard')->with('error', 'Shipment tidak valid untuk diselesaikan');
        }

        $earning = (int) ($shipment['courier_earning'] ?: floor(((int) $shipment['delivery_fee']) * self::DEFAULT_COURIER_EARNING_RATIO));

        $db->table('shipments')->where('id', $shipmentId)->update([
            'status' => 'DELIVERED',
            'delivered_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'courier_earning' => $earning,
        ]);
        (new ShipmentWorkflowService())->syncOrderStatus((int) $shipment['order_id']);

        $db->table('couriers')->where('id', $courier['id'])
            ->set('total_deliveries', 'total_deliveries + 1', false)
            ->set('total_earnings', 'total_earnings + ' . $earning, false)
            ->set('status', "'AVAILABLE'", false)
            ->update();

        return redirect()->to('/courier/dashboard')->with('success', 'Pengantaran selesai');
    }

    public function track(int $shipmentId)
    {
        $db = \Config\Database::connect();
        $courier = $db->table('couriers')->where('user_id', (int) session()->get('user_id'))->get()->getRowArray();
        if (!$courier) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Courier tidak ditemukan']);
        }

        $shipment = $db->table('shipments')->where('id', $shipmentId)->where('courier_id', $courier['id'])->get()->getRowArray();
        if (!$shipment) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Shipment tidak ditemukan']);
        }

        $latitude = (float) $this->request->getPost('latitude');
        $longitude = (float) $this->request->getPost('longitude');
        $accuracy = $this->request->getPost('accuracy');
        (new ShipmentWorkflowService())->recordTracking($shipmentId, (int) $courier['id'], $latitude, $longitude, $accuracy !== null && $accuracy !== '' ? (float) $accuracy : null);

        return $this->response->setJSON(['success' => true, 'message' => 'Lokasi tersimpan']);
    }

    private function changeShipmentStatus(int $shipmentId, string $expected, string $next, array $extra, string $message, ?string $courierStatus = null)
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
        if ($courierStatus) {
            $db->table('couriers')->where('id', $courier['id'])->update(['status' => $courierStatus, 'updated_at' => date('Y-m-d H:i:s')]);
        }
        (new ShipmentWorkflowService())->syncOrderStatus((int) $shipment['order_id']);

        return redirect()->to('/courier/dashboard')->with('success', $message);
    }
}
