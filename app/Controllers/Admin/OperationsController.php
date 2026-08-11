<?php

namespace App\Controllers\Admin;

class OperationsController extends BaseAdminController
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function orders()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('orders o')
            ->select('o.id, o.order_number, o.status, o.total, o.created_at, u.name as customer_name')
            ->join('users u', 'u.id = o.customer_id')
            ->orderBy('o.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'Orders',
            'Pantau order terbaru yang membutuhkan tindak lanjut operasi.',
            ['Order', 'Customer', 'Status', 'Total', 'Dibuat'],
            array_map(static fn (array $row) => [
                ['type' => 'text', 'value' => $row['order_number']],
                ['type' => 'text', 'value' => $row['customer_name']],
                ['type' => 'status', 'value' => $row['status']],
                ['type' => 'currency', 'value' => (int) $row['total']],
                ['type' => 'text', 'value' => $row['created_at']],
            ], $rows),
            'Belum ada order masuk.',
            'Transaksi customer akan muncul di sini setelah checkout.'
        );
    }

    public function products()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('products p')
            ->select("p.name, p.status, p.stock, p.price, s.name as store_name, (SELECT file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
            ->join('stores s', 's.id = p.store_id')
            ->orderBy('p.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'Products',
            'Monitor katalog, stok, dan performa listing UMKM.',
            ['Produk', 'Toko', 'Status', 'Stok', 'Harga'],
            array_map(static fn (array $row) => [
                ['type' => 'media', 'value' => $row['name'], 'image' => $row['primary_image'] ?? null],
                ['type' => 'text', 'value' => $row['store_name']],
                ['type' => 'status', 'value' => $row['status']],
                ['type' => 'text', 'value' => (string) $row['stock']],
                ['type' => 'currency', 'value' => (int) $row['price']],
            ], $rows),
            'Belum ada produk.',
            'Produk UMKM yang aktif maupun draft akan ditampilkan di modul ini.'
        );
    }

    public function categories()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('categories')
            ->select('name, slug, is_active, sort_order')
            ->orderBy('sort_order', 'ASC')
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'Categories',
            'Kelola struktur navigasi kategori marketplace.',
            ['Kategori', 'Slug', 'Status', 'Urutan'],
            array_map(static fn (array $row) => [
                ['type' => 'text', 'value' => $row['name']],
                ['type' => 'text', 'value' => $row['slug']],
                ['type' => 'status', 'value' => (int) $row['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE'],
                ['type' => 'text', 'value' => (string) $row['sort_order']],
            ], $rows),
            'Belum ada kategori.',
            'Tambahkan kategori untuk membantu customer menavigasi katalog.'
        );
    }

    public function stores()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('stores s')
            ->select('s.name, s.status, s.rating_avg, s.logo, u.business_name, u.verification_status')
            ->join('umkms u', 'u.id = s.umkm_id')
            ->orderBy('s.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'UMKM',
            'Pantau profil merchant, verifikasi, dan reputasi toko.',
            ['Toko', 'Pemilik UMKM', 'Verifikasi', 'Status', 'Rating'],
            array_map(static fn (array $row) => [
                ['type' => 'media', 'value' => $row['name'], 'image' => $row['logo'] ?? null],
                ['type' => 'text', 'value' => $row['business_name']],
                ['type' => 'status', 'value' => $row['verification_status']],
                ['type' => 'status', 'value' => $row['status']],
                ['type' => 'text', 'value' => number_format((float) $row['rating_avg'], 1)],
            ], $rows),
            'Belum ada toko UMKM.',
            'Toko yang terhubung ke akun UMKM akan tampil pada daftar ini.'
        );
    }

    public function couriers()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('couriers c')
            ->select('u.name, c.phone, c.status, c.verification_status, c.total_deliveries, c.total_earnings')
            ->join('users u', 'u.id = c.user_id')
            ->orderBy('c.updated_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'Couriers',
            'Lacak armada aktif, status online, dan performa pengantaran.',
            ['Kurir', 'Telepon', 'Status', 'Verifikasi', 'Performa'],
            array_map(static fn (array $row) => [
                ['type' => 'text', 'value' => $row['name']],
                ['type' => 'text', 'value' => $row['phone']],
                ['type' => 'status', 'value' => $row['status']],
                ['type' => 'status', 'value' => $row['verification_status']],
                ['type' => 'text', 'value' => $row['total_deliveries'] . ' pengiriman · ' . bm_currency((int) $row['total_earnings'])],
            ], $rows),
            'Belum ada kurir terdaftar.',
            'Kurir lapangan yang diverifikasi akan muncul di sini.'
        );
    }

    public function customers()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('users u')
            ->select('u.name, u.email, u.phone, u.last_login_at, COUNT(o.id) as total_orders')
            ->join('user_roles ur', 'ur.user_id = u.id')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('orders o', 'o.customer_id = u.id', 'left')
            ->where('r.slug', 'customer')
            ->groupBy('u.id, u.name, u.email, u.phone, u.last_login_at')
            ->orderBy('u.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'Customers',
            'Amati pelanggan aktif, histori transaksi, dan kontak utama.',
            ['Nama', 'Email', 'Telepon', 'Orders', 'Last Login'],
            array_map(static fn (array $row) => [
                ['type' => 'text', 'value' => $row['name']],
                ['type' => 'text', 'value' => $row['email']],
                ['type' => 'text', 'value' => $row['phone'] ?: '-'],
                ['type' => 'text', 'value' => (string) $row['total_orders']],
                ['type' => 'text', 'value' => $row['last_login_at'] ?: '-'],
            ], $rows),
            'Belum ada customer.',
            'Akun customer baru akan tercatat di modul pelanggan.'
        );
    }

    public function users()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('users u')
            ->select('u.name, u.email, u.is_active, GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") as roles')
            ->join('user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->groupBy('u.id, u.name, u.email, u.is_active')
            ->orderBy('u.created_at', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'Users / Admins',
            'Daftar akun sistem lintas peran untuk kebutuhan operasional.',
            ['Nama', 'Email', 'Status', 'Peran'],
            array_map(static fn (array $row) => [
                ['type' => 'text', 'value' => $row['name']],
                ['type' => 'text', 'value' => $row['email']],
                ['type' => 'status', 'value' => (int) $row['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE'],
                ['type' => 'text', 'value' => $row['roles'] ?: '-'],
            ], $rows),
            'Belum ada akun pengguna.',
            'Admin, UMKM, courier, dan customer akan direkap pada tabel ini.'
        );
    }

    public function auditLog()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $rows = $this->db->table('audit_logs a')
            ->select('a.action, a.entity_type, a.entity_id, a.created_at, u.name')
            ->join('users u', 'u.id = a.user_id', 'left')
            ->orderBy('a.created_at', 'DESC')
            ->limit(30)
            ->get()
            ->getResultArray();

        return $this->renderIndex(
            'Audit Log',
            'Jejak aktivitas admin dan perubahan konfigurasi marketplace.',
            ['Waktu', 'User', 'Aksi', 'Entitas'],
            array_map(static fn (array $row) => [
                ['type' => 'text', 'value' => $row['created_at']],
                ['type' => 'text', 'value' => $row['name'] ?: 'System'],
                ['type' => 'text', 'value' => $row['action']],
                ['type' => 'text', 'value' => trim(($row['entity_type'] ?: '-') . ' #' . ($row['entity_id'] ?: '-'))],
            ], $rows),
            'Belum ada aktivitas tercatat.',
            'Perubahan penting dari panel admin akan otomatis terekam.'
        );
    }

    private function renderIndex(string $title, string $description, array $columns, array $rows, string $emptyTitle, string $emptyDescription)
    {
        return view('admin/operations/index', compact('title', 'description', 'columns', 'rows', 'emptyTitle', 'emptyDescription'));
    }
}
