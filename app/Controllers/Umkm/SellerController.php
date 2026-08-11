<?php

namespace App\Controllers\Umkm;

use App\Controllers\BaseController;

class SellerController extends BaseController
{
    protected \CodeIgniter\Database\BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function products()
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $products = [];
        if (!empty($storeIds)) {
            $products = $this->db->table('products p')
                ->select("p.*, s.name as store_name, c.name as category_name, (SELECT file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
                ->join('stores s', 's.id = p.store_id')
                ->join('categories c', 'c.id = p.category_id')
                ->whereIn('p.store_id', $storeIds)
                ->orderBy('p.created_at', 'DESC')
                ->get()->getResultArray();
        }

        return view('umkm/products/index', [
            'title' => 'Produk UMKM',
            'products' => $products,
        ]);
    }

    public function createProduct()
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        return view('umkm/products/form', [
            'title' => 'Tambah Produk',
            'product' => null,
            'categories' => $this->db->table('categories')->where('is_active', 1)->orderBy('sort_order', 'ASC')->get()->getResultArray(),
            'stores' => $this->db->table('stores')->whereIn('id', $storeIds)->orderBy('name', 'ASC')->get()->getResultArray(),
            'images' => [],
        ]);
    }

    public function storeProduct()
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $rules = [
            'store_id' => 'required|integer',
            'category_id' => 'required|integer',
            'name' => 'required|min_length[3]',
            'price' => 'required|integer',
            'stock' => 'required|integer',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $storeId = (int) $this->request->getPost('store_id');
        if (!in_array($storeId, $storeIds, true)) {
            return redirect()->back()->withInput()->with('error', 'Store tidak valid');
        }

        $slug = url_title((string)$this->request->getPost('name'), '-', true) . '-' . substr(md5(uniqid((string) random_int(1, 9999), true)), 0, 6);

        $this->db->table('products')->insert([
            'store_id' => $storeId,
            'category_id' => (int) $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'slug' => $slug,
            'description' => $this->request->getPost('description'),
            'price' => (int) $this->request->getPost('price'),
            'stock' => (int) $this->request->getPost('stock'),
            'weight' => (int) ($this->request->getPost('weight') ?: 0),
            'status' => $this->request->getPost('status') ?: 'ACTIVE',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $productId = (int) $this->db->insertID();
        $this->saveProductImages($productId);

        return redirect()->to('/umkm/products')->with('success', 'Produk berhasil dibuat');
    }

    public function editProduct(int $id)
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $product = $this->db->table('products')->where('id', $id)->whereIn('store_id', $storeIds)->get()->getRowArray();
        if (!$product) {
            return redirect()->to('/umkm/products')->with('error', 'Produk tidak ditemukan');
        }

        $images = $this->db->table('product_images')->where('product_id', $id)->orderBy('is_primary', 'DESC')->orderBy('sort_order', 'ASC')->get()->getResultArray();

        return view('umkm/products/form', [
            'title' => 'Edit Produk',
            'product' => $product,
            'categories' => $this->db->table('categories')->where('is_active', 1)->orderBy('sort_order', 'ASC')->get()->getResultArray(),
            'stores' => $this->db->table('stores')->whereIn('id', $storeIds)->orderBy('name', 'ASC')->get()->getResultArray(),
            'images' => $images,
        ]);
    }

    public function updateProduct(int $id)
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $product = $this->db->table('products')->where('id', $id)->whereIn('store_id', $storeIds)->get()->getRowArray();
        if (!$product) {
            return redirect()->to('/umkm/products')->with('error', 'Produk tidak ditemukan');
        }

        $storeId = (int) $this->request->getPost('store_id');
        if (!in_array($storeId, $storeIds, true)) {
            return redirect()->back()->withInput()->with('error', 'Store tidak valid');
        }

        $this->db->table('products')->where('id', $id)->update([
            'store_id' => $storeId,
            'category_id' => (int) $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => (int) $this->request->getPost('price'),
            'stock' => (int) $this->request->getPost('stock'),
            'weight' => (int) ($this->request->getPost('weight') ?: 0),
            'status' => $this->request->getPost('status') ?: 'ACTIVE',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $primaryImageId = (int) ($this->request->getPost('primary_image_id') ?: 0);
        if ($primaryImageId > 0) {
            $this->db->table('product_images')->where('product_id', $id)->update(['is_primary' => 0]);
            $this->db->table('product_images')->where(['id' => $primaryImageId, 'product_id' => $id])->update(['is_primary' => 1]);
        }

        $this->saveProductImages($id);

        return redirect()->to('/umkm/products')->with('success', 'Produk berhasil diperbarui');
    }

    public function deleteProduct(int $id)
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $this->db->table('products')->where('id', $id)->whereIn('store_id', $storeIds)->update([
            'deleted_at' => date('Y-m-d H:i:s'),
            'status' => 'INACTIVE',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/umkm/products')->with('success', 'Produk dihapus');
    }

    public function toggleProduct(int $id)
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }
        $product = $this->db->table('products')->where('id', $id)->whereIn('store_id', $storeIds)->get()->getRowArray();
        if (!$product) {
            return redirect()->to('/umkm/products')->with('error', 'Produk tidak ditemukan');
        }

        $next = $product['status'] === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';
        $this->db->table('products')->where('id', $id)->update(['status' => $next, 'updated_at' => date('Y-m-d H:i:s')]);
        return redirect()->to('/umkm/products')->with('success', 'Status produk diperbarui');
    }

    public function orders()
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $status = (string) ($this->request->getGet('status') ?? '');
        $builder = $this->db->table('order_items oi')
            ->select('oi.order_id, oi.product_name, oi.quantity, oi.subtotal, o.order_number, o.status, o.created_at')
            ->join('orders o', 'o.id = oi.order_id')
            ->whereIn('oi.store_id', $storeIds)
            ->orderBy('o.created_at', 'DESC');

        if ($status !== '') {
            $builder->where('o.status', $status);
        }

        $orders = $builder->limit(50)->get()->getResultArray();

        return view('umkm/orders/index', [
            'title' => 'Pesanan Toko',
            'orders' => $orders,
            'status' => $status,
        ]);
    }

    public function updateOrderStatus(int $orderId)
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $allowed = ['PAID', 'PROCESSING', 'READY_FOR_PICKUP', 'COMPLETED'];
        $status = (string) $this->request->getPost('status');
        if (!in_array($status, $allowed, true)) {
            return redirect()->to('/umkm/orders')->with('error', 'Status tidak valid');
        }

        $exists = $this->db->table('order_items')->where('order_id', $orderId)->whereIn('store_id', $storeIds)->countAllResults();
        if (!$exists) {
            return redirect()->to('/umkm/orders')->with('error', 'Pesanan tidak ditemukan');
        }

        $this->db->table('orders')->where('id', $orderId)->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
        if ($status === 'READY_FOR_PICKUP') {
            $this->db->table('shipments')->where('order_id', $orderId)->where('status', 'PENDING')->update([
                'status' => 'READY_FOR_PICKUP',
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return redirect()->to('/umkm/orders')->with('success', 'Status pesanan diperbarui');
    }

    public function reports()
    {
        [$umkm, $storeIds] = $this->context();
        if (!$umkm) {
            return redirect()->to('/')->with('error', 'Data UMKM tidak ditemukan');
        }

        $today = date('Y-m-d');
        $monthStart = date('Y-m-01');

        $salesToday = !empty($storeIds) ? (int) ($this->db->table('order_items')->selectSum('subtotal')->whereIn('store_id', $storeIds)->where('DATE(created_at)', $today)->get()->getRowArray()['subtotal'] ?? 0) : 0;
        $salesMonth = !empty($storeIds) ? (int) ($this->db->table('order_items')->selectSum('subtotal')->whereIn('store_id', $storeIds)->where('DATE(created_at) >=', $monthStart)->where('DATE(created_at) <=', $today)->get()->getRowArray()['subtotal'] ?? 0) : 0;

        $topProducts = !empty($storeIds) ? $this->db->table('order_items')->select('product_name, SUM(quantity) as qty, SUM(subtotal) as sales')->whereIn('store_id', $storeIds)->groupBy('product_name')->orderBy('qty', 'DESC')->limit(10)->get()->getResultArray() : [];

        return view('umkm/reports/index', [
            'title' => 'Laporan UMKM',
            'salesToday' => $salesToday,
            'salesMonth' => $salesMonth,
            'topProducts' => $topProducts,
        ]);
    }

    public function storeProfile()
    {
        [$umkm, $storeIds, $stores] = $this->context(true);
        if (!$umkm || empty($stores)) {
            return redirect()->to('/')->with('error', 'Data toko tidak ditemukan');
        }

        return view('umkm/store/form', [
            'title' => 'Profil Toko',
            'store' => $stores[0],
        ]);
    }

    public function saveStoreProfile()
    {
        [$umkm, $storeIds, $stores] = $this->context(true);
        if (!$umkm || empty($stores)) {
            return redirect()->to('/')->with('error', 'Data toko tidak ditemukan');
        }

        $store = $stores[0];
        $logo = $this->uploadStoreFile('logo_file', $store['logo'] ?? null);
        $cover = $this->uploadStoreFile('cover_file', $store['cover_image'] ?? null);
        if (isset($logo['error'])) {
            return redirect()->back()->with('error', $logo['error']);
        }
        if (isset($cover['error'])) {
            return redirect()->back()->with('error', $cover['error']);
        }

        $this->db->table('stores')->where('id', $store['id'])->update([
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'address' => $this->request->getPost('address'),
            'district' => $this->request->getPost('district'),
            'city' => $this->request->getPost('city'),
            'status' => $this->request->getPost('status') ?: 'ACTIVE',
            'logo' => $logo['path'] ?? ($store['logo'] ?? null),
            'cover_image' => $cover['path'] ?? ($store['cover_image'] ?? null),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/umkm/store')->with('success', 'Profil toko diperbarui');
    }

    private function saveProductImages(int $productId): void
    {
        $files = $this->request->getFiles();
        $uploads = $files['images'] ?? [];
        if (!is_array($uploads)) {
            return;
        }

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'products';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $existingCount = $this->db->table('product_images')->where('product_id', $productId)->countAllResults();
        foreach ($uploads as $index => $file) {
            if (!$file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $mime = $file->getMimeType();
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                continue;
            }

            $name = $file->getRandomName();
            $file->move($targetDir, $name, true);
            $this->db->table('product_images')->insert([
                'product_id' => $productId,
                'file_path' => 'uploads/products/' . $name,
                'is_primary' => ($existingCount === 0 && $index === 0) ? 1 : 0,
                'sort_order' => $existingCount + $index + 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    private function uploadStoreFile(string $field, ?string $existingPath = null): array
    {
        $file = $this->request->getFile($field);
        if (!$file || !$file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return ['path' => $existingPath];
        }

        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return ['error' => 'File harus berupa gambar JPG/PNG/WEBP'];
        }

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'stores';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $name = $file->getRandomName();
        $file->move($targetDir, $name, true);
        return ['path' => 'uploads/stores/' . $name];
    }

    private function context(bool $withStores = false): array
    {
        $roles = session()->get('roles') ?? [];
        if (!in_array('umkm', $roles, true)) {
            return [null, [], []];
        }

        $userId = (int) session()->get('user_id');
        $umkm = $this->db->table('umkms')->where('user_id', $userId)->get()->getRowArray();
        if (!$umkm) {
            return [null, [], []];
        }

        $stores = $this->db->table('stores')->where('umkm_id', $umkm['id'])->get()->getResultArray();
        $storeIds = array_map(static fn ($row) => (int) $row['id'], $stores);

        if ($withStores) {
            return [$umkm, $storeIds, $stores];
        }

        return [$umkm, $storeIds];
    }
}
