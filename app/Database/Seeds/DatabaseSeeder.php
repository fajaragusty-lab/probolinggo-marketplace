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

        $db->table('roles')->insertBatch([
            ['name' => 'Super Admin', 'slug' => 'super_admin', 'created_at' => $now],
            ['name' => 'Government Admin', 'slug' => 'government_admin', 'created_at' => $now],
            ['name' => 'Customer', 'slug' => 'customer', 'created_at' => $now],
            ['name' => 'UMKM', 'slug' => 'umkm', 'created_at' => $now],
            ['name' => 'Courier', 'slug' => 'courier', 'created_at' => $now],
        ]);
        $roleMap = [];
        foreach ($db->table('roles')->get()->getResultArray() as $r) {
            $roleMap[$r['slug']] = $r['id'];
        }

        $users = [
            ['name' => 'Super Admin', 'email' => 'superadmin@marketplace.test', 'phone' => '08111111111', 'password' => $password, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Gov Admin', 'email' => 'govadmin@marketplace.test', 'phone' => '08122222222', 'password' => $password, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Budi Customer', 'email' => 'customer@marketplace.test', 'phone' => '08133333333', 'password' => $password, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Siti UMKM', 'email' => 'umkm1@marketplace.test', 'phone' => '08144444444', 'password' => $password, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Andi Courier', 'email' => 'courier1@marketplace.test', 'phone' => '08155555555', 'password' => $password, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Rina Customer', 'email' => 'customer2@marketplace.test', 'phone' => '08166666666', 'password' => $password, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Joko UMKM', 'email' => 'umkm2@marketplace.test', 'phone' => '08177777777', 'password' => $password, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ];
        $db->table('users')->insertBatch($users);
        $userMap = [];
        foreach ($db->table('users')->get()->getResultArray() as $u) {
            $userMap[$u['email']] = $u['id'];
        }

        $db->table('user_roles')->insertBatch([
            ['user_id' => $userMap['superadmin@marketplace.test'], 'role_id' => $roleMap['super_admin'], 'created_at' => $now],
            ['user_id' => $userMap['govadmin@marketplace.test'], 'role_id' => $roleMap['government_admin'], 'created_at' => $now],
            ['user_id' => $userMap['customer@marketplace.test'], 'role_id' => $roleMap['customer'], 'created_at' => $now],
            ['user_id' => $userMap['customer2@marketplace.test'], 'role_id' => $roleMap['customer'], 'created_at' => $now],
            ['user_id' => $userMap['umkm1@marketplace.test'], 'role_id' => $roleMap['umkm'], 'created_at' => $now],
            ['user_id' => $userMap['umkm2@marketplace.test'], 'role_id' => $roleMap['umkm'], 'created_at' => $now],
            ['user_id' => $userMap['courier1@marketplace.test'], 'role_id' => $roleMap['courier'], 'created_at' => $now],
        ]);

        $db->table('categories')->insertBatch([
            ['name' => 'Makanan', 'slug' => 'makanan', 'icon' => '🍜', 'sort_order' => 1, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Minuman', 'slug' => 'minuman', 'icon' => '🥤', 'sort_order' => 2, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Kerajinan', 'slug' => 'kerajinan', 'icon' => '🎨', 'sort_order' => 3, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => '👕', 'sort_order' => 4, 'is_active' => 1, 'created_at' => $now],
            ['name' => 'Pertanian', 'slug' => 'pertanian', 'icon' => '🌾', 'sort_order' => 5, 'is_active' => 1, 'created_at' => $now],
        ]);
        $catMap = [];
        foreach ($db->table('categories')->get()->getResultArray() as $c) {
            $catMap[$c['slug']] = $c['id'];
        }

        $db->table('umkms')->insert([
            'user_id' => $userMap['umkm1@marketplace.test'], 'business_name' => 'Keripik Singkong Bu Siti',
            'owner_name' => 'Siti Aminah', 'phone' => '08144444444',
            'address' => 'Jl. Soekarno Hatta No. 12', 'district' => 'Mayangan', 'city' => 'Probolinggo',
            'verification_status' => 'VERIFIED', 'verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $umkm1 = $db->insertID();
        $db->table('umkms')->insert([
            'user_id' => $userMap['umkm2@marketplace.test'], 'business_name' => 'Batik Probolinggo Joko',
            'owner_name' => 'Joko Santoso', 'phone' => '08177777777',
            'address' => 'Jl. Panglima Sudirman No. 45', 'district' => 'Kanigaran', 'city' => 'Probolinggo',
            'verification_status' => 'VERIFIED', 'verified_at' => $now, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $umkm2 = $db->insertID();

        $db->table('stores')->insert([
            'umkm_id' => $umkm1, 'name' => 'Toko Keripik Bu Siti', 'slug' => 'toko-keripik-bu-siti',
            'description' => 'Keripik singkong khas Probolinggo.', 'address' => 'Jl. Soekarno Hatta No. 12',
            'district' => 'Mayangan', 'city' => 'Probolinggo', 'status' => 'ACTIVE',
            'rating_avg' => 4.50, 'rating_count' => 12, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $store1 = $db->insertID();
        $db->table('stores')->insert([
            'umkm_id' => $umkm2, 'name' => 'Batik Joko Probolinggo', 'slug' => 'batik-joko-probolinggo',
            'description' => 'Batik tulis dan cap motif lokal.', 'address' => 'Jl. Panglima Sudirman No. 45',
            'district' => 'Kanigaran', 'city' => 'Probolinggo', 'status' => 'ACTIVE',
            'rating_avg' => 4.80, 'rating_count' => 8, 'created_at' => $now, 'updated_at' => $now,
        ]);
        $store2 = $db->insertID();

        $products = [
            ['store_id' => $store1, 'category_id' => $catMap['makanan'], 'name' => 'Keripik Singkong Original 250g', 'slug' => 'keripik-singkong-original-250g', 'description' => 'Keripik singkong original.', 'price' => 15000, 'stock' => 100, 'weight' => 250, 'status' => 'ACTIVE', 'is_featured' => 1, 'rating_avg' => 4.5, 'rating_count' => 10, 'sold_count' => 50, 'created_at' => $now, 'updated_at' => $now],
            ['store_id' => $store1, 'category_id' => $catMap['makanan'], 'name' => 'Keripik Singkong Pedas 250g', 'slug' => 'keripik-singkong-pedas-250g', 'description' => 'Keripik rasa pedas.', 'price' => 17000, 'stock' => 80, 'weight' => 250, 'status' => 'ACTIVE', 'is_featured' => 1, 'rating_avg' => 4.7, 'rating_count' => 15, 'sold_count' => 40, 'created_at' => $now, 'updated_at' => $now],
            ['store_id' => $store1, 'category_id' => $catMap['minuman'], 'name' => 'Wedang Uwuh Botol 500ml', 'slug' => 'wedang-uwuh-botol-500ml', 'description' => 'Wedang uwuh siap minum.', 'price' => 12000, 'stock' => 50, 'weight' => 500, 'status' => 'ACTIVE', 'is_featured' => 0, 'rating_avg' => 4.2, 'rating_count' => 5, 'sold_count' => 20, 'created_at' => $now, 'updated_at' => $now],
            ['store_id' => $store2, 'category_id' => $catMap['fashion'], 'name' => 'Kemeja Batik Motif Probolinggo', 'slug' => 'kemeja-batik-motif-probolinggo', 'description' => 'Kemeja batik tulis.', 'price' => 175000, 'stock' => 25, 'weight' => 300, 'status' => 'ACTIVE', 'is_featured' => 1, 'rating_avg' => 4.9, 'rating_count' => 7, 'sold_count' => 12, 'created_at' => $now, 'updated_at' => $now],
            ['store_id' => $store2, 'category_id' => $catMap['fashion'], 'name' => 'Selendang Batik Cap', 'slug' => 'selendang-batik-cap', 'description' => 'Selendang batik cap.', 'price' => 85000, 'stock' => 40, 'weight' => 150, 'status' => 'ACTIVE', 'is_featured' => 0, 'rating_avg' => 4.6, 'rating_count' => 4, 'sold_count' => 8, 'created_at' => $now, 'updated_at' => $now],
            ['store_id' => $store2, 'category_id' => $catMap['kerajinan'], 'name' => 'Tas Anyaman Pandan', 'slug' => 'tas-anyaman-pandan', 'description' => 'Tas anyaman handmade.', 'price' => 65000, 'stock' => 30, 'weight' => 200, 'status' => 'ACTIVE', 'is_featured' => 1, 'rating_avg' => 4.4, 'rating_count' => 6, 'sold_count' => 15, 'created_at' => $now, 'updated_at' => $now],
            ['store_id' => $store1, 'category_id' => $catMap['pertanian'], 'name' => 'Beras Organik 1kg', 'slug' => 'beras-organik-1kg', 'description' => 'Beras organik lokal.', 'price' => 22000, 'stock' => 200, 'weight' => 1000, 'status' => 'ACTIVE', 'is_featured' => 0, 'rating_avg' => 4.3, 'rating_count' => 9, 'sold_count' => 60, 'created_at' => $now, 'updated_at' => $now],
            ['store_id' => $store1, 'category_id' => $catMap['makanan'], 'name' => 'Dodol Probolinggo 200g', 'slug' => 'dodol-probolinggo-200g', 'description' => 'Dodol khas Probolinggo.', 'price' => 18000, 'stock' => 60, 'weight' => 200, 'status' => 'ACTIVE', 'is_featured' => 1, 'rating_avg' => 4.8, 'rating_count' => 20, 'sold_count' => 100, 'created_at' => $now, 'updated_at' => $now],
        ];
        $db->table('products')->insertBatch($products);

        $db->table('addresses')->insert([
            'user_id' => $userMap['customer@marketplace.test'],
            'label' => 'Rumah', 'recipient_name' => 'Budi Customer', 'phone' => '08133333333',
            'address' => 'Jl. Raya Dr. Sutomo No. 8', 'district' => 'Wonoasih', 'city' => 'Probolinggo',
            'postal_code' => '67237', 'is_default' => 1, 'created_at' => $now, 'updated_at' => $now,
        ]);

        $db->table('couriers')->insert([
            'user_id' => $userMap['courier1@marketplace.test'],
            'phone' => '08155555555', 'identity_number' => '3574010101900001',
            'verification_status' => 'VERIFIED', 'verified_at' => $now,
            'status' => 'OFFLINE', 'created_at' => $now, 'updated_at' => $now,
        ]);
        $courierId = $db->insertID();
        $db->table('vehicles')->insert([
            'courier_id' => $courierId, 'vehicle_type' => 'motor', 'brand' => 'Honda',
            'model' => 'Vario', 'license_plate' => 'N 1234 AB', 'is_active' => 1,
            'created_at' => $now, 'updated_at' => $now,
        ]);

        echo "DatabaseSeeder completed.\n";
    }
}
