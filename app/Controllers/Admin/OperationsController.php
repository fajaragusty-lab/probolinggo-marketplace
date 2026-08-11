<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;
use App\Services\AuditLogService;

class OperationsController extends BaseAdminController
{
    protected \CodeIgniter\Database\BaseConnection $db;
    protected UserModel $users;
    protected AuditLogService $audit;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->users = model(UserModel::class);
        $this->audit = new AuditLogService();
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

        $q = trim((string) $this->request->getGet('q'));
        $status = strtolower(trim((string) $this->request->getGet('status')));
        $editId = (int) $this->request->getGet('edit');

        $builder = $this->db->table('users u')
            ->select('u.id, u.name, u.email, u.phone, u.is_active, u.last_login_at, u.created_at, GROUP_CONCAT(r.name ORDER BY r.name SEPARATOR ", ") as roles, GROUP_CONCAT(r.slug ORDER BY r.slug SEPARATOR ",") as role_slugs')
            ->join('user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('u.deleted_at', null)
            ->groupBy('u.id, u.name, u.email, u.phone, u.is_active, u.last_login_at, u.created_at')
            ->orderBy('u.created_at', 'DESC')
            ->limit(40);

        if ($q !== '') {
            $builder->groupStart()
                ->like('u.name', $q)
                ->orLike('u.email', $q)
                ->orLike('u.phone', $q)
                ->groupEnd();
        }
        if ($status === 'active') {
            $builder->where('u.is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('u.is_active', 0);
        }

        $users = $builder->get()->getResultArray();
        $editing = $editId > 0 ? $this->userRecord($editId) : null;
        $roles = $this->db->table('roles')->select('id, name, slug')->orderBy('name', 'ASC')->get()->getResultArray();

        return view('admin/operations/users', [
            'users' => $users,
            'editing' => $editing,
            'roles' => $roles,
            'filters' => ['q' => $q, 'status' => $status],
        ]);
    }

    public function saveUser()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $id = (int) $this->request->getPost('id');
        $existing = $id > 0 ? $this->userRecord($id) : null;
        if ($id > 0 && !$existing) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
            'phone' => 'permit_empty|min_length[10]|max_length[20]',
            'password' => $id > 0 ? 'permit_empty|min_length[8]' : 'required|min_length[8]',
            'role_slug' => 'required|max_length[50]',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $duplicate = $this->db->table('users')
            ->where('email', $email)
            ->where('id !=', $id)
            ->where('deleted_at', null)
            ->countAllResults();
        if ($duplicate > 0) {
            return redirect()->back()->withInput()->with('error', 'Email user sudah digunakan');
        }

        $roleSlug = trim((string) $this->request->getPost('role_slug'));
        $role = $this->db->table('roles')->where('slug', $roleSlug)->get()->getRowArray();
        if (!$role) {
            return redirect()->back()->withInput()->with('error', 'Role tidak valid');
        }

        $payload = [
            'name' => trim((string) $this->request->getPost('name')),
            'email' => $email,
            'phone' => trim((string) $this->request->getPost('phone')) ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];
        $password = (string) $this->request->getPost('password');
        if ($password !== '') {
            $payload['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $this->db->transBegin();

        try {
            if ($id > 0) {
                $this->users->update($id, $payload);
                $this->db->table('user_roles')->where('user_id', $id)->delete();
                $this->db->table('user_roles')->insert([
                    'user_id' => $id,
                    'role_id' => (int) $role['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $this->audit->log('user_updated', 'user', $id, ['email' => $email, 'role' => $roleSlug]);
            } else {
                $id = (int) $this->users->insert($payload + ['password' => $payload['password'] ?? password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT)]);
                if ($id <= 0) {
                    throw new \RuntimeException('Gagal membuat user');
                }
                $this->db->table('user_roles')->insert([
                    'user_id' => $id,
                    'role_id' => (int) $role['id'],
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
                $this->audit->log('user_created', 'user', $id, ['email' => $email, 'role' => $roleSlug]);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi user gagal');
            }

            $this->db->transCommit();
            return redirect()->to('/admin/users')->with('success', $existing ? 'User diperbarui' : 'User ditambahkan');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function toggleUser(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $user = $this->userRecord($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }
        if ((int) session()->get('user_id') === $id) {
            return redirect()->to('/admin/users')->with('error', 'Tidak bisa menonaktifkan akun sendiri');
        }

        $next = (int) ($user['is_active'] ? 0 : 1);
        $this->users->update($id, ['is_active' => $next]);
        $this->audit->log($next ? 'user_activated' : 'user_deactivated', 'user', $id);

        return redirect()->to('/admin/users')->with('success', $next ? 'User diaktifkan' : 'User dinonaktifkan');
    }

    public function deleteUser(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $user = $this->userRecord($id);
        if (!$user) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan');
        }
        if ((int) session()->get('user_id') === $id) {
            return redirect()->to('/admin/users')->with('error', 'Tidak bisa menghapus akun sendiri');
        }

        $this->users->update($id, ['is_active' => 0]);
        $this->users->delete($id);
        $this->audit->log('user_deleted', 'user', $id, ['email' => $user['email']]);

        return redirect()->to('/admin/users')->with('success', 'User dihapus');
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

    private function userRecord(int $id): ?array
    {
        return $this->db->table('users u')
            ->select('u.id, u.name, u.email, u.phone, u.is_active, u.last_login_at, r.slug as role_slug')
            ->join('user_roles ur', 'ur.user_id = u.id', 'left')
            ->join('roles r', 'r.id = ur.role_id', 'left')
            ->where('u.id', $id)
            ->where('u.deleted_at', null)
            ->get()
            ->getRowArray();
    }
}
