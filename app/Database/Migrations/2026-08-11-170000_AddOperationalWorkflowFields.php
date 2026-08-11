<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOperationalWorkflowFields extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('latitude', 'addresses')) {
            $this->forge->addColumn('addresses', [
                'latitude' => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'postal_code'],
                'longitude' => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'latitude'],
                'location_recorded_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'longitude'],
            ]);
        }

        if (!$this->db->fieldExists('checkout_token', 'orders')) {
            $this->forge->addColumn('orders', [
                'checkout_token' => ['type' => 'VARCHAR', 'constraint' => 64, 'null' => true, 'after' => 'address_id'],
                'payment_method_code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'status'],
            ]);
            $this->db->query('CREATE UNIQUE INDEX orders_checkout_token_unique ON orders (checkout_token)');
        }

        if (!$this->db->fieldExists('delivery_latitude', 'shipments')) {
            $this->forge->addColumn('shipments', [
                'delivery_latitude' => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'delivery_address'],
                'delivery_longitude' => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true, 'after' => 'delivery_latitude'],
                'arrived_pickup_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'assigned_at'],
                'on_delivery_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'picked_up_at'],
                'arrived_destination_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'on_delivery_at'],
                'proof_uploaded_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'otp_verified_at'],
            ]);
        }

        $driver = $this->db->DBDriver;
        if ($driver === 'MySQLi') {
            $this->db->query("ALTER TABLE orders MODIFY status ENUM('PENDING_PAYMENT','COD_CONFIRMED','PAID','PROCESSING','READY_FOR_PICKUP','COURIER_ASSIGNED','PICKED_UP','ON_DELIVERY','DELIVERED','COMPLETED','FEEDBACK','CANCELLED') NOT NULL DEFAULT 'PENDING_PAYMENT'");
            $this->db->query("ALTER TABLE shipments MODIFY status ENUM('PENDING','READY_FOR_PICKUP','ACCEPTED','ARRIVED_PICKUP','PICKED_UP','ON_DELIVERY','ARRIVED_DESTINATION','OTP_VERIFIED','PROOF_UPLOADED','DELIVERED','CANCELLED') NOT NULL DEFAULT 'PENDING'");
            $this->db->query("ALTER TABLE payments MODIFY status ENUM('PENDING','PAID','FAILED','CANCELLED','EXPIRED','REFUNDED') NOT NULL DEFAULT 'PENDING'");
        }

        $this->db->query('CREATE INDEX shipments_store_status_idx ON shipments (store_id, status)');
        $this->db->query('CREATE INDEX shipment_tracking_shipment_recorded_idx ON shipment_tracking (shipment_id, recorded_at)');
        $this->db->query('CREATE INDEX feedbacks_customer_status_idx ON feedbacks (customer_id, status)');
    }

    public function down()
    {
        if ($this->db->fieldExists('latitude', 'addresses')) {
            $this->forge->dropColumn('addresses', ['latitude', 'longitude', 'location_recorded_at']);
        }

        if ($this->db->fieldExists('checkout_token', 'orders')) {
            $this->forge->dropColumn('orders', ['checkout_token', 'payment_method_code']);
        }

        if ($this->db->fieldExists('delivery_latitude', 'shipments')) {
            $this->forge->dropColumn('shipments', [
                'delivery_latitude',
                'delivery_longitude',
                'arrived_pickup_at',
                'on_delivery_at',
                'arrived_destination_at',
                'proof_uploaded_at',
            ]);
        }
    }
}
