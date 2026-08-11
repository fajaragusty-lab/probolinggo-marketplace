<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $now = date('Y-m-d H:i:s');
        $password = password_hash('password', PASSWORD_DEFAULT);

        $roles = [
            'super_admin' => 'Super Admin',
            'government_admin' => 'Government Admin',
            'customer' => 'Customer',
            'umkm' => 'UMKM',
            'courier' => 'Courier',
        ];
        foreach ($roles as $slug => $name) {
            $this->upsert('roles', ['slug' => $slug], ['name' => $name, 'slug' => $slug, 'updated_at' => $now], ['created_at' => $now]);
        }
        $roleMap = [];
        foreach ($db->table('roles')->get()->getResultArray() as $role) {
            $roleMap[$role['slug']] = (int) $role['id'];
        }

        $users = [
            'superadmin@marketplace.test' => ['name' => 'Super Admin', 'phone' => '08111111111', 'role' => 'super_admin'],
            'govadmin@marketplace.test' => ['name' => 'Gov Admin', 'phone' => '08122222222', 'role' => 'government_admin'],
            'customer@marketplace.test' => ['name' => 'Budi Customer', 'phone' => '08133333333', 'role' => 'customer'],
            'customer2@marketplace.test' => ['name' => 'Rina Customer', 'phone' => '08166666666', 'role' => 'customer'],
            'umkm1@marketplace.test' => ['name' => 'Siti UMKM', 'phone' => '08144444444', 'role' => 'umkm'],
            'umkm2@marketplace.test' => ['name' => 'Joko UMKM', 'phone' => '08177777777', 'role' => 'umkm'],
            'courier1@marketplace.test' => ['name' => 'Andi Courier', 'phone' => '08155555555', 'role' => 'courier'],
        ];
        foreach ($users as $email => $user) {
            $userId = $this->upsert('users', ['email' => $email], [
                'name' => $user['name'],
                'email' => $email,
                'phone' => $user['phone'],
                'password' => $password,
                'is_active' => 1,
                'updated_at' => $now,
            ], ['created_at' => $now]);
            $this->upsert('user_roles', ['user_id' => $userId, 'role_id' => $roleMap[$user['role']]], [
                'user_id' => $userId,
                'role_id' => $roleMap[$user['role']],
            ], ['created_at' => $now]);
            $userMap[$email] = $userId;
        }

        $categories = [
            'makanan' => ['name' => 'Makanan', 'icon' => '🍜', 'sort_order' => 1],
            'minuman' => ['name' => 'Minuman', 'icon' => '🥤', 'sort_order' => 2],
            'kerajinan' => ['name' => 'Kerajinan', 'icon' => '🎨', 'sort_order' => 3],
            'fashion' => ['name' => 'Fashion', 'icon' => '👕', 'sort_order' => 4],
            'pertanian' => ['name' => 'Pertanian', 'icon' => '🌾', 'sort_order' => 5],
        ];
        $catMap = [];
        foreach ($categories as $slug => $category) {
            $catMap[$slug] = $this->upsert('categories', ['slug' => $slug], [
                'name' => $category['name'],
                'slug' => $slug,
                'icon' => $category['icon'],
                'sort_order' => $category['sort_order'],
                'is_active' => 1,
                'updated_at' => $now,
            ], ['created_at' => $now]);
        }

        $umkm1 = $this->upsert('umkms', ['user_id' => $userMap['umkm1@marketplace.test']], [
            'user_id' => $userMap['umkm1@marketplace.test'],
            'business_name' => 'Keripik Singkong Bu Siti',
            'owner_name' => 'Siti Aminah',
            'phone' => '08144444444',
            'address' => 'Jl. Soekarno Hatta No. 12',
            'district' => 'Mayangan',
            'city' => 'Probolinggo',
            'verification_status' => 'VERIFIED',
            'verified_at' => $now,
            'updated_at' => $now,
        ], ['created_at' => $now]);
        $umkm2 = $this->upsert('umkms', ['user_id' => $userMap['umkm2@marketplace.test']], [
            'user_id' => $userMap['umkm2@marketplace.test'],
            'business_name' => 'Batik Probolinggo Joko',
            'owner_name' => 'Joko Santoso',
            'phone' => '08177777777',
            'address' => 'Jl. Panglima Sudirman No. 45',
            'district' => 'Kanigaran',
            'city' => 'Probolinggo',
            'verification_status' => 'VERIFIED',
            'verified_at' => $now,
            'updated_at' => $now,
        ], ['created_at' => $now]);

        $store1 = $this->upsert('stores', ['slug' => 'toko-keripik-bu-siti'], [
            'umkm_id' => $umkm1,
            'name' => 'Toko Keripik Bu Siti',
            'slug' => 'toko-keripik-bu-siti',
            'description' => 'Keripik singkong khas Probolinggo.',
            'address' => 'Jl. Soekarno Hatta No. 12',
            'district' => 'Mayangan',
            'city' => 'Probolinggo',
            'status' => 'ACTIVE',
            'rating_avg' => 4.50,
            'rating_count' => 12,
            'updated_at' => $now,
        ], ['created_at' => $now]);
        $store2 = $this->upsert('stores', ['slug' => 'batik-joko-probolinggo'], [
            'umkm_id' => $umkm2,
            'name' => 'Batik Joko Probolinggo',
            'slug' => 'batik-joko-probolinggo',
            'description' => 'Batik tulis dan cap motif lokal.',
            'address' => 'Jl. Panglima Sudirman No. 45',
            'district' => 'Kanigaran',
            'city' => 'Probolinggo',
            'status' => 'ACTIVE',
            'rating_avg' => 4.80,
            'rating_count' => 8,
            'updated_at' => $now,
        ], ['created_at' => $now]);

        $products = [
            'keripik-singkong-original-250g' => ['store_id' => $store1, 'category_id' => $catMap['makanan'], 'name' => 'Keripik Singkong Original 250g', 'description' => 'Keripik singkong original.', 'price' => 15000, 'stock' => 100, 'weight' => 250, 'is_featured' => 1, 'rating_avg' => 4.5, 'rating_count' => 10, 'sold_count' => 50],
            'keripik-singkong-pedas-250g' => ['store_id' => $store1, 'category_id' => $catMap['makanan'], 'name' => 'Keripik Singkong Pedas 250g', 'description' => 'Keripik rasa pedas.', 'price' => 17000, 'stock' => 80, 'weight' => 250, 'is_featured' => 1, 'rating_avg' => 4.7, 'rating_count' => 15, 'sold_count' => 40],
            'wedang-uwuh-botol-500ml' => ['store_id' => $store1, 'category_id' => $catMap['minuman'], 'name' => 'Wedang Uwuh Botol 500ml', 'description' => 'Wedang uwuh siap minum.', 'price' => 12000, 'stock' => 50, 'weight' => 500, 'is_featured' => 0, 'rating_avg' => 4.2, 'rating_count' => 5, 'sold_count' => 20],
            'kemeja-batik-motif-probolinggo' => ['store_id' => $store2, 'category_id' => $catMap['fashion'], 'name' => 'Kemeja Batik Motif Probolinggo', 'description' => 'Kemeja batik tulis.', 'price' => 175000, 'stock' => 25, 'weight' => 300, 'is_featured' => 1, 'rating_avg' => 4.9, 'rating_count' => 7, 'sold_count' => 12],
            'selendang-batik-cap' => ['store_id' => $store2, 'category_id' => $catMap['fashion'], 'name' => 'Selendang Batik Cap', 'description' => 'Selendang batik cap.', 'price' => 85000, 'stock' => 40, 'weight' => 150, 'is_featured' => 0, 'rating_avg' => 4.6, 'rating_count' => 4, 'sold_count' => 8],
            'tas-anyaman-pandan' => ['store_id' => $store2, 'category_id' => $catMap['kerajinan'], 'name' => 'Tas Anyaman Pandan', 'description' => 'Tas anyaman handmade.', 'price' => 65000, 'stock' => 30, 'weight' => 200, 'is_featured' => 1, 'rating_avg' => 4.4, 'rating_count' => 6, 'sold_count' => 15],
            'beras-organik-1kg' => ['store_id' => $store1, 'category_id' => $catMap['pertanian'], 'name' => 'Beras Organik 1kg', 'description' => 'Beras organik lokal.', 'price' => 22000, 'stock' => 200, 'weight' => 1000, 'is_featured' => 0, 'rating_avg' => 4.3, 'rating_count' => 9, 'sold_count' => 60],
            'dodol-probolinggo-200g' => ['store_id' => $store1, 'category_id' => $catMap['makanan'], 'name' => 'Dodol Probolinggo 200g', 'description' => 'Dodol khas Probolinggo.', 'price' => 18000, 'stock' => 60, 'weight' => 200, 'is_featured' => 1, 'rating_avg' => 4.8, 'rating_count' => 20, 'sold_count' => 100],
        ];
        $productMap = [];
        foreach ($products as $slug => $product) {
            $productMap[$slug] = $this->upsert('products', ['slug' => $slug], array_merge($product, [
                'slug' => $slug,
                'status' => 'ACTIVE',
                'updated_at' => $now,
            ]), ['created_at' => $now]);
        }

        $address1 = $this->upsert('addresses', ['user_id' => $userMap['customer@marketplace.test'], 'label' => 'Rumah'], [
            'user_id' => $userMap['customer@marketplace.test'],
            'label' => 'Rumah',
            'recipient_name' => 'Budi Customer',
            'phone' => '08133333333',
            'address' => 'Jl. Raya Dr. Sutomo No. 8',
            'district' => 'Wonoasih',
            'city' => 'Probolinggo',
            'postal_code' => '67237',
            'latitude' => -7.7543201,
            'longitude' => 113.2150975,
            'location_recorded_at' => $now,
            'is_default' => 1,
            'updated_at' => $now,
        ], ['created_at' => $now]);
        $address2 = $this->upsert('addresses', ['user_id' => $userMap['customer2@marketplace.test'], 'label' => 'Kantor'], [
            'user_id' => $userMap['customer2@marketplace.test'],
            'label' => 'Kantor',
            'recipient_name' => 'Rina Customer',
            'phone' => '08166666666',
            'address' => 'Jl. Suroyo No. 17',
            'district' => 'Kanigaran',
            'city' => 'Probolinggo',
            'postal_code' => '67213',
            'latitude' => -7.7521750,
            'longitude' => 113.2137610,
            'location_recorded_at' => $now,
            'is_default' => 1,
            'updated_at' => $now,
        ], ['created_at' => $now]);

        $courierId = $this->upsert('couriers', ['user_id' => $userMap['courier1@marketplace.test']], [
            'user_id' => $userMap['courier1@marketplace.test'],
            'phone' => '08155555555',
            'identity_number' => '3574010101900001',
            'verification_status' => 'VERIFIED',
            'verified_at' => $now,
            'status' => 'AVAILABLE',
            'current_lat' => -7.7511200,
            'current_lng' => 113.2141700,
            'updated_at' => $now,
        ], ['created_at' => $now]);
        $this->upsert('vehicles', ['courier_id' => $courierId, 'license_plate' => 'N 1234 AB'], [
            'courier_id' => $courierId,
            'vehicle_type' => 'motor',
            'brand' => 'Honda',
            'model' => 'Vario',
            'license_plate' => 'N 1234 AB',
            'is_active' => 1,
            'updated_at' => $now,
        ], ['created_at' => $now]);

        $featuredProducts = array_slice(array_values($productMap), 0, 4);
        foreach ($featuredProducts as $index => $productId) {
            $this->upsert('featured_products', ['product_id' => $productId], [
                'product_id' => $productId,
                'sort_order' => $index + 1,
                'is_active' => 1,
                'updated_at' => $now,
            ], ['created_at' => $now]);
        }

        foreach ([$store1, $store2] as $index => $storeId) {
            $this->upsert('featured_stores', ['store_id' => $storeId], [
                'store_id' => $storeId,
                'sort_order' => $index + 1,
                'is_active' => 1,
                'updated_at' => $now,
            ], ['created_at' => $now]);
        }

        $banners = [
            'Belanja Produk Lokal Probolinggo' => ['subtitle' => 'Dukung UMKM Kota Probolinggo', 'cta_label' => 'Jelajahi Produk', 'cta_url' => '/search', 'sort_order' => 1],
            'Pengiriman Cepat Dalam Kota' => ['subtitle' => 'Lacak pesanan Anda secara real-time', 'cta_label' => 'Lihat Pesanan', 'cta_url' => '/orders', 'sort_order' => 2],
        ];
        foreach ($banners as $title => $banner) {
            $this->upsert('banners', ['title' => $title], array_merge($banner, [
                'title' => $title,
                'is_active' => 1,
                'created_by' => $userMap['superadmin@marketplace.test'],
                'updated_by' => $userMap['superadmin@marketplace.test'],
                'updated_at' => $now,
            ]), ['created_at' => $now]);
        }

        $settings = [
            'app_name' => 'BERSOLEKMART',
            'app_tagline' => 'Marketplace UMKM Kota Probolinggo',
            'general_currency' => 'IDR',
            'marketplace_shipping_base_fee' => '10000',
            'marketplace_minimum_order' => '0',
            'maintenance_mode' => '0',
            'checkout_payment_methods' => 'bank_transfer,qris,cod',
            'checkout_cod' => '1',
            'delivery_fee_label' => 'Kurir lokal BersolekMart',
            'contact_email' => 'support@bersolekmart.test',
            'homepage_sections_json' => json_encode([
                ['key' => 'featured', 'label' => 'Produk Unggulan', 'sort_order' => 1, 'enabled' => true],
                ['key' => 'stores', 'label' => 'Toko Pilihan UMKM', 'sort_order' => 2, 'enabled' => true],
                ['key' => 'trending', 'label' => 'Sedang Trending', 'sort_order' => 3, 'enabled' => true],
                ['key' => 'latest', 'label' => 'Produk Terbaru', 'sort_order' => 4, 'enabled' => true],
            ]),
        ];
        foreach ($settings as $key => $value) {
            $this->upsert('marketplace_settings', ['setting_key' => $key], [
                'setting_key' => $key,
                'setting_value' => $value,
                'updated_at' => $now,
            ], ['created_at' => $now]);
        }

        $paymentMethods = [
            'bank_transfer' => ['provider' => 'development', 'method_name' => 'Transfer Bank', 'config_json' => json_encode(['instruction' => 'Transfer manual ke rekening yang tertera di dashboard admin.'])],
            'qris' => ['provider' => 'development', 'method_name' => 'QRIS', 'config_json' => json_encode(['instruction' => 'Scan QRIS setelah gateway dihubungkan.'])],
            'cod' => ['provider' => 'bersolekmart', 'method_name' => 'Cash on Delivery (COD)', 'config_json' => json_encode(['instruction' => 'Bayar tunai kepada kurir saat pesanan diterima.'])],
        ];
        foreach ($paymentMethods as $methodCode => $method) {
            $this->upsert('payment_methods', ['provider' => $method['provider'], 'method_code' => $methodCode], [
                'provider' => $method['provider'],
                'method_code' => $methodCode,
                'method_name' => $method['method_name'],
                'is_active' => 1,
                'config_json' => $method['config_json'],
                'updated_at' => $now,
            ], ['created_at' => $now]);
        }

        $completedOrderId = $this->upsert('orders', ['order_number' => 'BM-DEMO-COMPLETE'], [
            'order_number' => 'BM-DEMO-COMPLETE',
            'customer_id' => $userMap['customer@marketplace.test'],
            'address_id' => $address1,
            'checkout_token' => 'seed-complete-order',
            'status' => 'COMPLETED',
            'payment_method_code' => 'cod',
            'subtotal' => 50000,
            'shipping_fee' => 10000,
            'discount' => 0,
            'total' => 60000,
            'notes' => 'Contoh order selesai',
            'paid_at' => $now,
            'completed_at' => $now,
            'updated_at' => $now,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))]);
        $this->upsert('payments', ['payment_number' => 'PAY-DEMO-COMPLETE'], [
            'order_id' => $completedOrderId,
            'payment_number' => 'PAY-DEMO-COMPLETE',
            'provider' => 'bersolekmart',
            'amount' => 60000,
            'status' => 'PAID',
            'paid_at' => $now,
            'metadata' => json_encode(['method_code' => 'cod', 'method_name' => 'Cash on Delivery (COD)']),
            'updated_at' => $now,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))]);
        $this->upsert('order_items', ['order_id' => $completedOrderId, 'product_id' => $productMap['keripik-singkong-original-250g']], [
            'order_id' => $completedOrderId,
            'store_id' => $store1,
            'product_id' => $productMap['keripik-singkong-original-250g'],
            'product_name' => 'Keripik Singkong Original 250g',
            'product_price' => 15000,
            'quantity' => 2,
            'subtotal' => 30000,
            'weight' => 250,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))]);
        $this->upsert('order_items', ['order_id' => $completedOrderId, 'product_id' => $productMap['dodol-probolinggo-200g']], [
            'order_id' => $completedOrderId,
            'store_id' => $store1,
            'product_id' => $productMap['dodol-probolinggo-200g'],
            'product_name' => 'Dodol Probolinggo 200g',
            'product_price' => 20000,
            'quantity' => 1,
            'subtotal' => 20000,
            'weight' => 200,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))]);
        $completedShipmentId = $this->upsert('shipments', ['shipment_number' => 'SH-DEMO-COMPLETE'], [
            'order_id' => $completedOrderId,
            'store_id' => $store1,
            'courier_id' => $courierId,
            'shipment_number' => 'SH-DEMO-COMPLETE',
            'status' => 'DELIVERED',
            'delivery_fee' => 10000,
            'courier_earning' => 7000,
            'pickup_address' => 'Jl. Soekarno Hatta No. 12, Mayangan',
            'delivery_address' => 'Jl. Raya Dr. Sutomo No. 8, Wonoasih, Probolinggo',
            'delivery_latitude' => -7.7543201,
            'delivery_longitude' => 113.2150975,
            'otp_code' => '123456',
            'assigned_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours')),
            'arrived_pickup_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +10 minutes')),
            'picked_up_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +20 minutes')),
            'on_delivery_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +30 minutes')),
            'arrived_destination_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +45 minutes')),
            'otp_verified_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +50 minutes')),
            'proof_uploaded_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +51 minutes')),
            'delivered_at' => date('Y-m-d H:i:s', strtotime('-1 day +3 hours')),
            'updated_at' => $now,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours'))]);
        $this->upsert('shipment_tracking', ['shipment_id' => $completedShipmentId, 'recorded_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +40 minutes'))], [
            'shipment_id' => $completedShipmentId,
            'courier_id' => $courierId,
            'latitude' => -7.7532101,
            'longitude' => 113.2141120,
            'accuracy' => 15,
            'recorded_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +40 minutes')),
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-1 day +2 hours +40 minutes'))]);

        $readyOrderId = $this->upsert('orders', ['order_number' => 'BM-DEMO-READY'], [
            'order_number' => 'BM-DEMO-READY',
            'customer_id' => $userMap['customer2@marketplace.test'],
            'address_id' => $address2,
            'checkout_token' => 'seed-ready-order',
            'status' => 'READY_FOR_PICKUP',
            'payment_method_code' => 'cod',
            'subtotal' => 85000,
            'shipping_fee' => 10000,
            'discount' => 0,
            'total' => 95000,
            'notes' => 'Contoh order siap pickup',
            'paid_at' => $now,
            'updated_at' => $now,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))]);
        $this->upsert('payments', ['payment_number' => 'PAY-DEMO-READY'], [
            'order_id' => $readyOrderId,
            'payment_number' => 'PAY-DEMO-READY',
            'provider' => 'bersolekmart',
            'amount' => 95000,
            'status' => 'PAID',
            'paid_at' => $now,
            'metadata' => json_encode(['method_code' => 'cod', 'method_name' => 'Cash on Delivery (COD)']),
            'updated_at' => $now,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))]);
        $this->upsert('order_items', ['order_id' => $readyOrderId, 'product_id' => $productMap['selendang-batik-cap']], [
            'order_id' => $readyOrderId,
            'store_id' => $store2,
            'product_id' => $productMap['selendang-batik-cap'],
            'product_name' => 'Selendang Batik Cap',
            'product_price' => 85000,
            'quantity' => 1,
            'subtotal' => 85000,
            'weight' => 150,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))]);
        $this->upsert('shipments', ['shipment_number' => 'SH-DEMO-READY'], [
            'order_id' => $readyOrderId,
            'store_id' => $store2,
            'shipment_number' => 'SH-DEMO-READY',
            'status' => 'READY_FOR_PICKUP',
            'delivery_fee' => 10000,
            'pickup_address' => 'Jl. Panglima Sudirman No. 45, Kanigaran',
            'delivery_address' => 'Jl. Suroyo No. 17, Kanigaran, Probolinggo',
            'delivery_latitude' => -7.7521750,
            'delivery_longitude' => 113.2137610,
            'otp_code' => '654321',
            'updated_at' => $now,
        ], ['created_at' => date('Y-m-d H:i:s', strtotime('-90 minutes'))]);

        $this->upsert('feedbacks', ['order_id' => $completedOrderId, 'product_id' => $productMap['keripik-singkong-original-250g'], 'customer_id' => $userMap['customer@marketplace.test']], [
            'order_id' => $completedOrderId,
            'customer_id' => $userMap['customer@marketplace.test'],
            'product_id' => $productMap['keripik-singkong-original-250g'],
            'store_id' => $store1,
            'rating' => 5,
            'comment' => 'Pesanan datang cepat dan produk segar.',
            'status' => 'APPROVED',
            'created_at' => date('Y-m-d H:i:s', strtotime('-20 hours')),
            'updated_at' => date('Y-m-d H:i:s', strtotime('-20 hours')),
        ], []);

        echo "DatabaseSeeder completed.\n";
    }

    private function upsert(string $table, array $where, array $data, array $createDefaults): int
    {
        $builder = $this->db->table($table);
        $row = $builder->where($where)->get()->getRowArray();
        if ($row) {
            $builder->where('id', $row['id'])->update(array_merge($createDefaults, $data));
            return (int) $row['id'];
        }

        $builder->insert(array_merge($createDefaults, $data));
        return (int) $this->db->insertID();
    }
}
