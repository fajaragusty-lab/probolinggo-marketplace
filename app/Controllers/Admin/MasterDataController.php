<?php

namespace App\Controllers\Admin;

use App\Models\UserModel;
use App\Services\AuditLogService;

class MasterDataController extends BaseAdminController
{
    protected \CodeIgniter\Database\BaseConnection $db;
    protected AuditLogService $audit;
    protected UserModel $users;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->audit = new AuditLogService();
        $this->users = model(UserModel::class);
    }

    public function categories()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $q = trim((string) ($this->request->getGet('q') ?? ''));
        $status = (string) ($this->request->getGet('status') ?? '');
        $editId = (int) ($this->request->getGet('edit') ?? 0);

        $builder = $this->db->table('categories c')
            ->select('c.*, COUNT(p.id) as product_count')
            ->join('products p', 'p.category_id = c.id AND p.deleted_at IS NULL', 'left')
            ->groupBy('c.id, c.name, c.slug, c.description, c.icon, c.sort_order, c.is_active, c.created_at, c.updated_at');

        if ($q !== '') {
            $builder->groupStart()
                ->like('c.name', $q)
                ->orLike('c.slug', $q)
                ->orLike('c.description', $q)
                ->groupEnd();
        }

        if ($status === 'active') {
            $builder->where('c.is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('c.is_active', 0);
        }

        $categories = $builder
            ->orderBy('c.sort_order', 'ASC')
            ->orderBy('c.name', 'ASC')
            ->get()
            ->getResultArray();

        $editing = $editId > 0 ? $this->db->table('categories')->where('id', $editId)->get()->getRowArray() : null;

        return view('admin/master_data/categories', [
            'title' => 'Category Management',
            'categories' => $categories,
            'editing' => $editing,
            'filters' => ['q' => $q, 'status' => $status],
            'stats' => [
                'total' => (int) $this->db->table('categories')->countAllResults(),
                'active' => (int) $this->db->table('categories')->where('is_active', 1)->countAllResults(),
                'products' => (int) $this->db->table('products')->where('deleted_at', null)->countAllResults(),
            ],
        ]);
    }

    public function saveCategory()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $id = (int) $this->request->getPost('id');
        $existing = $id > 0 ? $this->db->table('categories')->where('id', $id)->get()->getRowArray() : null;
        if ($id > 0 && !$existing) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'slug' => 'permit_empty|max_length[120]',
            'icon' => 'permit_empty|max_length[50]',
            'sort_order' => 'permit_empty|integer',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $slug = $this->slugify((string) ($this->request->getPost('slug') ?: $this->request->getPost('name')));
        if ($slug === '') {
            return redirect()->back()->withInput()->with('error', 'Slug kategori tidak valid');
        }

        $duplicate = $this->db->table('categories')
            ->where('slug', $slug)
            ->where('id !=', $id)
            ->countAllResults();
        if ($duplicate > 0) {
            return redirect()->back()->withInput()->with('error', 'Slug kategori sudah digunakan');
        }

        $data = [
            'name' => trim((string) $this->request->getPost('name')),
            'slug' => $slug,
            'description' => trim((string) $this->request->getPost('description')) ?: null,
            'icon' => trim((string) $this->request->getPost('icon')) ?: null,
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            $this->db->table('categories')->where('id', $id)->update($data);
            $this->audit->log('category_updated', 'category', $id, ['name' => $data['name']]);
            return redirect()->to('/admin/categories')->with('success', 'Kategori diperbarui');
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('categories')->insert($data);
        $newId = (int) $this->db->insertID();
        $this->audit->log('category_created', 'category', $newId, ['name' => $data['name']]);

        return redirect()->to('/admin/categories')->with('success', 'Kategori ditambahkan');
    }

    public function toggleCategory(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $category = $this->db->table('categories')->where('id', $id)->get()->getRowArray();
        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan');
        }

        $next = (int) ($category['is_active'] ? 0 : 1);
        $this->db->table('categories')->where('id', $id)->update([
            'is_active' => $next,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->audit->log($next ? 'category_activated' : 'category_deactivated', 'category', $id);

        return redirect()->to('/admin/categories')->with('success', $next ? 'Kategori diaktifkan' : 'Kategori dinonaktifkan');
    }

    public function deleteCategory(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $category = $this->db->table('categories')->where('id', $id)->get()->getRowArray();
        if (!$category) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori tidak ditemukan');
        }

        $productsCount = (int) $this->db->table('products')->where('category_id', $id)->where('deleted_at', null)->countAllResults();
        if ($productsCount > 0) {
            return redirect()->to('/admin/categories')->with('error', 'Kategori masih dipakai produk aktif, nonaktifkan atau pindahkan produknya terlebih dahulu');
        }

        $this->db->table('categories')->where('id', $id)->delete();
        $this->audit->log('category_deleted', 'category', $id, ['name' => $category['name']]);

        return redirect()->to('/admin/categories')->with('success', 'Kategori dihapus');
    }

    public function customers()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $q = trim((string) ($this->request->getGet('q') ?? ''));
        $status = (string) ($this->request->getGet('status') ?? '');
        $editId = (int) ($this->request->getGet('edit') ?? 0);

        $builder = $this->db->table('users u')
            ->select('u.id, u.name, u.email, u.phone, u.is_active, u.created_at, u.last_login_at, COUNT(DISTINCT o.id) as total_orders, COUNT(DISTINCT a.id) as total_addresses')
            ->join('user_roles ur', 'ur.user_id = u.id')
            ->join('roles r', 'r.id = ur.role_id')
            ->join('orders o', 'o.customer_id = u.id', 'left')
            ->join('addresses a', 'a.user_id = u.id', 'left')
            ->where('u.deleted_at', null)
            ->where('r.slug', 'customer')
            ->groupBy('u.id, u.name, u.email, u.phone, u.is_active, u.created_at, u.last_login_at');

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

        $customers = $builder
            ->orderBy('u.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $editing = $editId > 0 ? $this->customerRecord($editId) : null;
        $addresses = $editing ? $this->db->table('addresses')->where('user_id', $editId)->orderBy('is_default', 'DESC')->get()->getResultArray() : [];

        return view('admin/master_data/customers', [
            'title' => 'Customer Management',
            'customers' => $customers,
            'editing' => $editing,
            'addresses' => $addresses,
            'filters' => ['q' => $q, 'status' => $status],
            'stats' => [
                'total' => (int) $this->customerCount(),
                'active' => (int) $this->customerCount(1),
                'orders' => (int) $this->db->table('orders')->countAllResults(),
            ],
        ]);
    }

    public function saveCustomer()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $id = (int) $this->request->getPost('id');
        $existing = $id > 0 ? $this->customerRecord($id) : null;
        if ($id > 0 && !$existing) {
            return redirect()->to('/admin/customers')->with('error', 'Customer tidak ditemukan');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[150]',
            'email' => 'required|valid_email|max_length[150]',
            'phone' => 'permit_empty|min_length[10]|max_length[20]',
            'password' => $id > 0 ? 'permit_empty|min_length[8]' : 'required|min_length[8]',
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
            return redirect()->back()->withInput()->with('error', 'Email customer sudah digunakan');
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
                $this->audit->log('customer_updated', 'customer', $id, ['email' => $email]);
            } else {
                $payload['password'] = $payload['password'] ?? password_hash(bin2hex(random_bytes(8)), PASSWORD_DEFAULT);
                $id = (int) $this->users->insert($payload);
                if ($id <= 0 || !$this->users->assignRole($id, 'customer')) {
                    throw new \RuntimeException('Gagal membuat customer');
                }
                $this->audit->log('customer_created', 'customer', $id, ['email' => $email]);
            }

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaksi customer gagal');
            }

            $this->db->transCommit();
            return redirect()->to('/admin/customers')->with('success', $existing ? 'Customer diperbarui' : 'Customer ditambahkan');
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function toggleCustomer(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $customer = $this->customerRecord($id);
        if (!$customer) {
            return redirect()->to('/admin/customers')->with('error', 'Customer tidak ditemukan');
        }

        $next = (int) ($customer['is_active'] ? 0 : 1);
        $this->users->update($id, ['is_active' => $next]);
        $this->audit->log($next ? 'customer_activated' : 'customer_deactivated', 'customer', $id);

        return redirect()->to('/admin/customers')->with('success', $next ? 'Customer diaktifkan' : 'Customer dinonaktifkan');
    }

    public function deleteCustomer(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $customer = $this->customerRecord($id);
        if (!$customer) {
            return redirect()->to('/admin/customers')->with('error', 'Customer tidak ditemukan');
        }

        $this->users->update($id, ['is_active' => 0]);
        $this->users->delete($id);
        $this->audit->log('customer_deleted', 'customer', $id, ['email' => $customer['email']]);

        return redirect()->to('/admin/customers')->with('success', 'Customer dihapus');
    }

    public function paymentMethods()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $q = trim((string) ($this->request->getGet('q') ?? ''));
        $status = (string) ($this->request->getGet('status') ?? '');
        $editId = (int) ($this->request->getGet('edit') ?? 0);

        $builder = $this->db->table('payment_methods pm')
            ->select('pm.*, COUNT(o.id) as total_orders')
            ->join('orders o', 'o.payment_method_code = pm.method_code', 'left')
            ->groupBy('pm.id, pm.provider, pm.method_code, pm.method_name, pm.is_active, pm.config_json, pm.created_at, pm.updated_at');

        if ($q !== '') {
            $builder->groupStart()
                ->like('pm.provider', $q)
                ->orLike('pm.method_code', $q)
                ->orLike('pm.method_name', $q)
                ->groupEnd();
        }

        if ($status === 'active') {
            $builder->where('pm.is_active', 1);
        } elseif ($status === 'inactive') {
            $builder->where('pm.is_active', 0);
        }

        $methods = $builder
            ->orderBy('pm.is_active', 'DESC')
            ->orderBy('pm.method_name', 'ASC')
            ->get()
            ->getResultArray();

        $editing = $editId > 0 ? $this->db->table('payment_methods')->where('id', $editId)->get()->getRowArray() : null;

        return view('admin/master_data/payment_methods', [
            'title' => 'Payment Methods',
            'methods' => $methods,
            'editing' => $editing,
            'filters' => ['q' => $q, 'status' => $status],
            'stats' => [
                'total' => (int) $this->db->table('payment_methods')->countAllResults(),
                'active' => (int) $this->db->table('payment_methods')->where('is_active', 1)->countAllResults(),
                'payments' => (int) $this->db->table('payments')->countAllResults(),
            ],
        ]);
    }

    public function savePaymentMethod()
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $id = (int) $this->request->getPost('id');
        $existing = $id > 0 ? $this->db->table('payment_methods')->where('id', $id)->get()->getRowArray() : null;
        if ($id > 0 && !$existing) {
            return redirect()->to('/admin/payment-methods')->with('error', 'Metode pembayaran tidak ditemukan');
        }

        $rules = [
            'provider' => 'required|min_length[2]|max_length[50]',
            'method_code' => 'required|min_length[2]|max_length[50]',
            'method_name' => 'required|min_length[3]|max_length[100]',
            'config_json' => 'permit_empty',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $provider = strtolower(trim((string) $this->request->getPost('provider')));
        $methodCode = strtolower(trim((string) $this->request->getPost('method_code')));
        $configJson = trim((string) $this->request->getPost('config_json'));

        if ($configJson !== '') {
            json_decode($configJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect()->back()->withInput()->with('error', 'Config JSON tidak valid');
            }
        }

        $duplicate = $this->db->table('payment_methods')
            ->where('provider', $provider)
            ->where('method_code', $methodCode)
            ->where('id !=', $id)
            ->countAllResults();
        if ($duplicate > 0) {
            return redirect()->back()->withInput()->with('error', 'Provider dan code pembayaran sudah terdaftar');
        }

        $data = [
            'provider' => $provider,
            'method_code' => $methodCode,
            'method_name' => trim((string) $this->request->getPost('method_name')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'config_json' => $configJson !== '' ? $configJson : null,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            $this->db->table('payment_methods')->where('id', $id)->update($data);
            $this->audit->log('payment_method_updated', 'payment_method', $id, ['code' => $methodCode]);
            return redirect()->to('/admin/payment-methods')->with('success', 'Metode pembayaran diperbarui');
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $this->db->table('payment_methods')->insert($data);
        $newId = (int) $this->db->insertID();
        $this->audit->log('payment_method_created', 'payment_method', $newId, ['code' => $methodCode]);

        return redirect()->to('/admin/payment-methods')->with('success', 'Metode pembayaran ditambahkan');
    }

    public function togglePaymentMethod(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $method = $this->db->table('payment_methods')->where('id', $id)->get()->getRowArray();
        if (!$method) {
            return redirect()->to('/admin/payment-methods')->with('error', 'Metode pembayaran tidak ditemukan');
        }

        $next = (int) ($method['is_active'] ? 0 : 1);
        $this->db->table('payment_methods')->where('id', $id)->update([
            'is_active' => $next,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->audit->log($next ? 'payment_method_activated' : 'payment_method_deactivated', 'payment_method', $id);

        return redirect()->to('/admin/payment-methods')->with('success', $next ? 'Metode pembayaran diaktifkan' : 'Metode pembayaran dinonaktifkan');
    }

    public function deletePaymentMethod(int $id)
    {
        if ($guard = $this->guard()) {
            return $guard;
        }

        $method = $this->db->table('payment_methods')->where('id', $id)->get()->getRowArray();
        if (!$method) {
            return redirect()->to('/admin/payment-methods')->with('error', 'Metode pembayaran tidak ditemukan');
        }

        $usageCount = (int) $this->db->table('orders')->where('payment_method_code', $method['method_code'])->countAllResults();
        if ($usageCount > 0) {
            return redirect()->to('/admin/payment-methods')->with('error', 'Metode pembayaran sudah dipakai transaksi, nonaktifkan saja untuk menjaga histori');
        }

        $this->db->table('payment_methods')->where('id', $id)->delete();
        $this->audit->log('payment_method_deleted', 'payment_method', $id, ['code' => $method['method_code']]);

        return redirect()->to('/admin/payment-methods')->with('success', 'Metode pembayaran dihapus');
    }

    private function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
        return trim($value, '-');
    }

    private function customerRecord(int $id): ?array
    {
        return $this->db->table('users u')
            ->select('u.*')
            ->join('user_roles ur', 'ur.user_id = u.id')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('u.id', $id)
            ->where('r.slug', 'customer')
            ->where('u.deleted_at', null)
            ->get()
            ->getRowArray();
    }

    private function customerCount(?int $active = null): int
    {
        $builder = $this->db->table('users u')
            ->join('user_roles ur', 'ur.user_id = u.id')
            ->join('roles r', 'r.id = ur.role_id')
            ->where('u.deleted_at', null)
            ->where('r.slug', 'customer');

        if ($active !== null) {
            $builder->where('u.is_active', $active);
        }

        return (int) $builder->countAllResults();
    }
}
