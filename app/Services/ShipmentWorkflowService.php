<?php

namespace App\Services;

class ShipmentWorkflowService
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function syncOrderStatus(int $orderId): void
    {
        $order = $this->db->table('orders')->where('id', $orderId)->get()->getRowArray();
        if (!$order) {
            return;
        }

        $payment = $this->db->table('payments')->where('order_id', $orderId)->orderBy('id', 'DESC')->get()->getRowArray();
        $shipments = $this->db->table('shipments')->where('order_id', $orderId)->get()->getResultArray();
        $statuses = array_values(array_unique(array_column($shipments, 'status')));
        $nextStatus = $order['status'];

        if ($payment && $payment['status'] === 'FAILED') {
            $nextStatus = 'CANCELLED';
        } elseif ($shipments !== [] && count(array_diff($statuses, ['DELIVERED'])) === 0) {
            $nextStatus = 'COMPLETED';
        } elseif ($this->containsAny($statuses, ['PROOF_UPLOADED', 'OTP_VERIFIED', 'ARRIVED_DESTINATION', 'ON_DELIVERY'])) {
            $nextStatus = 'ON_DELIVERY';
        } elseif ($this->containsAny($statuses, ['PICKED_UP'])) {
            $nextStatus = 'PICKED_UP';
        } elseif ($this->containsAny($statuses, ['ACCEPTED', 'ARRIVED_PICKUP'])) {
            $nextStatus = 'COURIER_ASSIGNED';
        } elseif ($shipments !== [] && count(array_diff($statuses, ['READY_FOR_PICKUP'])) === 0) {
            $nextStatus = 'READY_FOR_PICKUP';
        } elseif ($this->containsAny($statuses, ['READY_FOR_PICKUP'])) {
            $nextStatus = 'PROCESSING';
        } elseif (($order['payment_method_code'] ?? null) === 'cod') {
            $nextStatus = 'COD_CONFIRMED';
        } elseif ($payment && $payment['status'] === 'PAID') {
            $nextStatus = 'PAID';
        } else {
            $nextStatus = 'PENDING_PAYMENT';
        }

        $payload = [
            'status' => $nextStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($nextStatus === 'COMPLETED' && empty($order['completed_at'])) {
            $payload['completed_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('orders')->where('id', $orderId)->update($payload);

        if (($order['payment_method_code'] ?? null) === 'cod' && $nextStatus === 'COMPLETED' && $payment && $payment['status'] !== 'PAID') {
            $this->db->table('payments')->where('id', $payment['id'])->update([
                'status' => 'PAID',
                'paid_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function buildTimeline(array $shipment): array
    {
        $steps = [
            'PENDING' => ['label' => 'Order masuk ke antrian pengiriman', 'time' => $shipment['created_at'] ?? null],
            'READY_FOR_PICKUP' => ['label' => 'Siap diambil dari UMKM', 'time' => $shipment['updated_at'] ?? null],
            'ACCEPTED' => ['label' => 'Kurir menerima job', 'time' => $shipment['assigned_at'] ?? null],
            'ARRIVED_PICKUP' => ['label' => 'Kurir tiba di titik pickup', 'time' => $shipment['arrived_pickup_at'] ?? null],
            'PICKED_UP' => ['label' => 'Pesanan telah diambil kurir', 'time' => $shipment['picked_up_at'] ?? null],
            'ON_DELIVERY' => ['label' => 'Kurir menuju alamat tujuan', 'time' => $shipment['on_delivery_at'] ?? null],
            'ARRIVED_DESTINATION' => ['label' => 'Kurir tiba di alamat tujuan', 'time' => $shipment['arrived_destination_at'] ?? null],
            'OTP_VERIFIED' => ['label' => 'OTP penerima tervalidasi', 'time' => $shipment['otp_verified_at'] ?? null],
            'PROOF_UPLOADED' => ['label' => 'Bukti pengiriman diunggah', 'time' => $shipment['proof_uploaded_at'] ?? null],
            'DELIVERED' => ['label' => 'Pengiriman selesai', 'time' => $shipment['delivered_at'] ?? null],
        ];

        $currentRank = $this->rank($shipment['status'] ?? 'PENDING');
        $timeline = [];
        foreach ($steps as $status => $step) {
            $timeline[] = [
                'status' => $status,
                'label' => $step['label'],
                'time' => $step['time'],
                'completed' => $currentRank >= $this->rank($status),
            ];
        }

        return $timeline;
    }

    public function recordTracking(int $shipmentId, int $courierId, float $latitude, float $longitude, ?float $accuracy = null): void
    {
        $now = date('Y-m-d H:i:s');
        $this->db->table('shipment_tracking')->insert([
            'shipment_id' => $shipmentId,
            'courier_id' => $courierId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $accuracy,
            'recorded_at' => $now,
            'created_at' => $now,
        ]);

        $this->db->table('couriers')->where('id', $courierId)->update([
            'current_lat' => $latitude,
            'current_lng' => $longitude,
            'updated_at' => $now,
        ]);
    }

    public function latestTracking(int $shipmentId): ?array
    {
        return $this->db->table('shipment_tracking')
            ->where('shipment_id', $shipmentId)
            ->orderBy('recorded_at', 'DESC')
            ->get()
            ->getRowArray();
    }

    private function containsAny(array $statuses, array $needles): bool
    {
        return count(array_intersect($statuses, $needles)) > 0;
    }

    private function rank(string $status): int
    {
        return match ($status) {
            'PENDING' => 1,
            'READY_FOR_PICKUP' => 2,
            'ACCEPTED' => 3,
            'ARRIVED_PICKUP' => 4,
            'PICKED_UP' => 5,
            'ON_DELIVERY' => 6,
            'ARRIVED_DESTINATION' => 7,
            'OTP_VERIFIED' => 8,
            'PROOF_UPLOADED' => 9,
            'DELIVERED' => 10,
            default => 0,
        };
    }
}
