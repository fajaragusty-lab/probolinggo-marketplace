<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBersolekMartTables extends Migration
{
    public function up()
    {
        // roles
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'slug'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('roles', true);

        // users
        $this->forge->addField([
            'id'                => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'              => ['type' => 'VARCHAR', 'constraint' => 150],
            'email'             => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'             => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'password'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'avatar'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'email_verified_at' => ['type' => 'DATETIME', 'null' => true],
            'last_login_at'     => ['type' => 'DATETIME', 'null' => true],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users', true);

        // user_roles
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'role_id'    => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'role_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_roles', true);

        // categories
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'description' => ['type' => 'TEXT', 'null' => true],
            'icon'        => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'sort_order'  => ['type' => 'INT', 'default' => 0],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('categories', true);

        // umkms
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'             => ['type' => 'INT', 'unsigned' => true],
            'business_name'       => ['type' => 'VARCHAR', 'constraint' => 200],
            'owner_name'          => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'               => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'             => ['type' => 'TEXT'],
            'district'            => ['type' => 'VARCHAR', 'constraint' => 100],
            'city'                => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Probolinggo'],
            'verification_status' => ['type' => 'ENUM', 'constraint' => ['PENDING', 'VERIFIED', 'REJECTED', 'SUSPENDED'], 'default' => 'PENDING'],
            'verified_at'         => ['type' => 'DATETIME', 'null' => true],
            'verified_by'         => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'rejection_reason'    => ['type' => 'TEXT', 'null' => true],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('umkms', true);

        // stores
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'umkm_id'      => ['type' => 'INT', 'unsigned' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 200],
            'slug'         => ['type' => 'VARCHAR', 'constraint' => 220],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'logo'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cover_image'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'address'      => ['type' => 'TEXT'],
            'district'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'city'         => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Probolinggo'],
            'status'       => ['type' => 'ENUM', 'constraint' => ['ACTIVE', 'INACTIVE', 'SUSPENDED'], 'default' => 'ACTIVE'],
            'rating_avg'   => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 0],
            'rating_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('umkm_id', 'umkms', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('stores', true);

        // products
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'store_id'     => ['type' => 'INT', 'unsigned' => true],
            'category_id'  => ['type' => 'INT', 'unsigned' => true],
            'name'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'         => ['type' => 'VARCHAR', 'constraint' => 280],
            'description'  => ['type' => 'TEXT', 'null' => true],
            'price'        => ['type' => 'BIGINT', 'unsigned' => true],
            'stock'        => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'weight'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'status'       => ['type' => 'ENUM', 'constraint' => ['PENDING', 'ACTIVE', 'INACTIVE', 'REJECTED'], 'default' => 'PENDING'],
            'is_featured'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'rating_avg'   => ['type' => 'DECIMAL', 'constraint' => '3,2', 'default' => 0],
            'rating_count' => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'sold_count'   => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('store_id', 'stores', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addKey('status');
        $this->forge->createTable('products', true);

        // product_images
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'file_path'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'is_primary' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_images', true);

        // addresses
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'        => ['type' => 'INT', 'unsigned' => true],
            'label'          => ['type' => 'VARCHAR', 'constraint' => 50],
            'recipient_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'        => ['type' => 'TEXT'],
            'district'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'city'           => ['type' => 'VARCHAR', 'constraint' => 100, 'default' => 'Probolinggo'],
            'postal_code'    => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'is_default'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('addresses', true);

        // carts
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('carts', true);

        // cart_items
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'cart_id'    => ['type' => 'INT', 'unsigned' => true],
            'product_id' => ['type' => 'INT', 'unsigned' => true],
            'quantity'   => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'price'      => ['type' => 'BIGINT', 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['cart_id', 'product_id']);
        $this->forge->addForeignKey('cart_id', 'carts', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('cart_items', true);

        // orders
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_number' => ['type' => 'VARCHAR', 'constraint' => 30],
            'customer_id'  => ['type' => 'INT', 'unsigned' => true],
            'address_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'status'       => ['type' => 'ENUM', 'constraint' => [
                'PENDING_PAYMENT', 'PAID', 'PROCESSING', 'READY_FOR_PICKUP',
                'PICKED_UP', 'ON_DELIVERY', 'DELIVERED', 'COMPLETED', 'FEEDBACK', 'CANCELLED'
            ], 'default' => 'PENDING_PAYMENT'],
            'subtotal'     => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
            'shipping_fee' => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
            'discount'     => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
            'total'        => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
            'notes'        => ['type' => 'TEXT', 'null' => true],
            'paid_at'      => ['type' => 'DATETIME', 'null' => true],
            'completed_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('order_number');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addKey(['customer_id', 'status']);
        $this->forge->createTable('orders', true);

        // order_items
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'      => ['type' => 'INT', 'unsigned' => true],
            'store_id'      => ['type' => 'INT', 'unsigned' => true],
            'product_id'    => ['type' => 'INT', 'unsigned' => true],
            'product_name'  => ['type' => 'VARCHAR', 'constraint' => 255],
            'product_price' => ['type' => 'BIGINT', 'unsigned' => true],
            'quantity'      => ['type' => 'INT', 'unsigned' => true],
            'subtotal'      => ['type' => 'BIGINT', 'unsigned' => true],
            'weight'        => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('store_id', 'stores', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('order_items', true);

        // payments
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'       => ['type' => 'INT', 'unsigned' => true],
            'payment_number' => ['type' => 'VARCHAR', 'constraint' => 40],
            'provider'       => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'development'],
            'amount'         => ['type' => 'BIGINT', 'unsigned' => true],
            'status'         => ['type' => 'ENUM', 'constraint' => ['PENDING', 'PAID', 'FAILED', 'EXPIRED', 'REFUNDED'], 'default' => 'PENDING'],
            'external_id'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'paid_at'        => ['type' => 'DATETIME', 'null' => true],
            'metadata'       => ['type' => 'JSON', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('payment_number');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payments', true);

        // payment_transactions
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'payment_id'     => ['type' => 'INT', 'unsigned' => true],
            'transaction_id' => ['type' => 'VARCHAR', 'constraint' => 100],
            'event_type'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'payload'        => ['type' => 'JSON', 'null' => true],
            'signature'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_processed'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'processed_at'   => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('transaction_id');
        $this->forge->addForeignKey('payment_id', 'payments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payment_transactions', true);

        // couriers
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'             => ['type' => 'INT', 'unsigned' => true],
            'identity_number'     => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'profile_photo'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'phone'               => ['type' => 'VARCHAR', 'constraint' => 20],
            'verification_status' => ['type' => 'ENUM', 'constraint' => ['PENDING', 'VERIFIED', 'REJECTED', 'SUSPENDED'], 'default' => 'PENDING'],
            'verified_at'         => ['type' => 'DATETIME', 'null' => true],
            'status'              => ['type' => 'ENUM', 'constraint' => ['OFFLINE', 'ONLINE', 'AVAILABLE', 'ASSIGNED', 'PICKUP', 'ON_DELIVERY'], 'default' => 'OFFLINE'],
            'current_lat'         => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'current_lng'         => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'total_deliveries'    => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'total_earnings'      => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
            'created_at'          => ['type' => 'DATETIME', 'null' => true],
            'updated_at'          => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('couriers', true);

        // vehicles
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'courier_id'    => ['type' => 'INT', 'unsigned' => true],
            'vehicle_type'  => ['type' => 'VARCHAR', 'constraint' => 50],
            'brand'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'model'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'license_plate' => ['type' => 'VARCHAR', 'constraint' => 20],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('courier_id', 'couriers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('vehicles', true);

        // shipments
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'         => ['type' => 'INT', 'unsigned' => true],
            'store_id'         => ['type' => 'INT', 'unsigned' => true],
            'courier_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'shipment_number'  => ['type' => 'VARCHAR', 'constraint' => 30],
            'status'           => ['type' => 'ENUM', 'constraint' => [
                'PENDING', 'READY_FOR_PICKUP', 'ASSIGNED', 'PICKED_UP', 'ON_DELIVERY', 'DELIVERED', 'CANCELLED'
            ], 'default' => 'PENDING'],
            'delivery_fee'     => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
            'courier_earning'  => ['type' => 'BIGINT', 'unsigned' => true, 'default' => 0],
            'pickup_address'   => ['type' => 'TEXT', 'null' => true],
            'delivery_address' => ['type' => 'TEXT', 'null' => true],
            'otp_code'         => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'otp_verified_at'  => ['type' => 'DATETIME', 'null' => true],
            'proof_image'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'assigned_at'      => ['type' => 'DATETIME', 'null' => true],
            'picked_up_at'     => ['type' => 'DATETIME', 'null' => true],
            'delivered_at'     => ['type' => 'DATETIME', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('shipment_number');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('store_id', 'stores', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->addForeignKey('courier_id', 'couriers', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addKey('status');
        $this->forge->createTable('shipments', true);

        // shipment_tracking
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'shipment_id' => ['type' => 'INT', 'unsigned' => true],
            'courier_id'  => ['type' => 'INT', 'unsigned' => true],
            'latitude'    => ['type' => 'DECIMAL', 'constraint' => '10,7'],
            'longitude'   => ['type' => 'DECIMAL', 'constraint' => '10,7'],
            'accuracy'    => ['type' => 'FLOAT', 'null' => true],
            'recorded_at' => ['type' => 'DATETIME'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('shipment_id', 'shipments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('courier_id', 'couriers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shipment_tracking', true);

        // feedbacks
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'order_id'     => ['type' => 'INT', 'unsigned' => true],
            'customer_id'  => ['type' => 'INT', 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'unsigned' => true],
            'store_id'     => ['type' => 'INT', 'unsigned' => true],
            'rating'       => ['type' => 'TINYINT', 'unsigned' => true],
            'comment'      => ['type' => 'TEXT', 'null' => true],
            'status'       => ['type' => 'ENUM', 'constraint' => ['PENDING', 'APPROVED', 'REJECTED'], 'default' => 'PENDING'],
            'moderated_by' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'moderated_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['order_id', 'product_id', 'customer_id']);
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('customer_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('store_id', 'stores', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('feedbacks', true);

        // feedback_images
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'feedback_id' => ['type' => 'INT', 'unsigned' => true],
            'file_path'   => ['type' => 'VARCHAR', 'constraint' => 255],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('feedback_id', 'feedbacks', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('feedback_images', true);

        // notifications
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],
            'type'       => ['type' => 'VARCHAR', 'constraint' => 50],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 200],
            'message'    => ['type' => 'TEXT'],
            'data'       => ['type' => 'JSON', 'null' => true],
            'is_read'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'read_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifications', true);

        // audit_logs
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'action'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'entity_type' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'entity_id'   => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'metadata'    => ['type' => 'JSON', 'null' => true],
            'ip_address'  => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->createTable('audit_logs', true);
    }

    public function down()
    {
        $tables = [
            'audit_logs', 'notifications', 'feedback_images', 'feedbacks',
            'shipment_tracking', 'shipments', 'vehicles', 'couriers',
            'payment_transactions', 'payments', 'order_items', 'orders',
            'cart_items', 'carts', 'addresses', 'product_images', 'products',
            'stores', 'umkms', 'categories', 'user_roles', 'users', 'roles',
        ];
        foreach ($tables as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
