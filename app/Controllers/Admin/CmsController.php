<?php

namespace App\Controllers\Admin;

use App\Services\AuditLogService;
use App\Services\MarketplaceSettingsService;

class CmsController extends BaseAdminController
{
    protected \CodeIgniter\Database\BaseConnection $db;
    protected AuditLogService $audit;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
        $this->audit = new AuditLogService();
    }

    public function homepage()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        return view('admin/cms/homepage', [
            'title' => 'Homepage CMS',
            'banners' => $this->activeBanners(),
            'featuredProducts' => $this->getFeaturedProductsData(),
            'featuredStores' => $this->getFeaturedStoresData(),
        ]);
    }

    public function banners()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $editId = (int) ($this->request->getGet('edit') ?? 0);
        $editBanner = null;
        if ($editId > 0) {
            $editBanner = $this->db->table('banners')->where('id', $editId)->get()->getRowArray();
        }

        return view('admin/cms/banners', [
            'title' => 'Banner Management',
            'banners' => $this->db->table('banners')->orderBy('sort_order', 'ASC')->orderBy('id', 'DESC')->get()->getResultArray(),
            'editBanner' => $editBanner,
        ]);
    }

    public function saveBanner()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }

        $rules = [
            'title' => 'required|min_length[3]|max_length[150]',
            'cta_url' => 'permit_empty|max_length[255]',
            'sort_order' => 'permit_empty|integer',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $ctaUrl = trim((string) ($this->request->getPost('cta_url') ?? ''));
        if ($ctaUrl !== '' && !preg_match('#^(https?://|/)#i', $ctaUrl)) {
            return redirect()->back()->withInput()->with('error', 'CTA URL harus URL absolut atau path relatif yang diawali /');
        }

        $id = (int) $this->request->getPost('id');
        $existing = $id > 0 ? $this->db->table('banners')->where('id', $id)->get()->getRowArray() : null;

        $desktopImage = $this->uploadBannerFile('image_file', $existing['image'] ?? null);
        if (isset($desktopImage['error'])) {
            return redirect()->back()->withInput()->with('error', $desktopImage['error']);
        }
        $mobileImage = $this->uploadBannerFile('mobile_image_file', $existing['mobile_image'] ?? null);
        if (isset($mobileImage['error'])) {
            return redirect()->back()->withInput()->with('error', $mobileImage['error']);
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'image' => $desktopImage['path'] ?? ($existing['image'] ?? null),
            'mobile_image' => $mobileImage['path'] ?? ($existing['mobile_image'] ?? null),
            'cta_label' => $this->request->getPost('cta_label') ?: null,
            'cta_url' => $ctaUrl !== '' ? $ctaUrl : null,
            'sort_order' => (int) ($this->request->getPost('sort_order') ?: 0),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'starts_at' => $this->request->getPost('starts_at') ?: null,
            'ends_at' => $this->request->getPost('ends_at') ?: null,
            'updated_by' => (int) session()->get('user_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($id > 0) {
            $this->db->table('banners')->where('id', $id)->update($data);
            $this->audit->log('banner_updated', 'banner', $id, ['title' => $data['title']]);
        } else {
            $data['created_by'] = (int) session()->get('user_id');
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('banners')->insert($data);
            $this->audit->log('banner_created', 'banner', (int) $this->db->insertID(), ['title' => $data['title']]);
        }

        return redirect()->to('/admin/banners')->with('success', 'Banner saved');
    }

    public function deleteBanner(int $id)
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }

        $banner = $this->db->table('banners')->where('id', $id)->get()->getRowArray();
        if (!$banner) {
            return redirect()->to('/admin/banners')->with('error', 'Banner tidak ditemukan');
        }

        $this->db->table('banners')->where('id', $id)->delete();
        $this->audit->log('banner_deleted', 'banner', $id, ['title' => $banner['title']]);
        return redirect()->to('/admin/banners')->with('success', 'Banner deleted');
    }

    public function duplicateBanner(int $id)
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }

        $banner = $this->db->table('banners')->where('id', $id)->get()->getRowArray();
        if (!$banner) {
            return redirect()->to('/admin/banners')->with('error', 'Banner tidak ditemukan');
        }

        unset($banner['id']);
        $banner['title'] = $banner['title'] . ' (Copy)';
        $banner['is_active'] = 0;
        $banner['created_by'] = (int) session()->get('user_id');
        $banner['updated_by'] = (int) session()->get('user_id');
        $banner['created_at'] = date('Y-m-d H:i:s');
        $banner['updated_at'] = date('Y-m-d H:i:s');
        $banner['sort_order'] = ((int) $banner['sort_order']) + 1;

        $this->db->table('banners')->insert($banner);
        $newId = (int) $this->db->insertID();
        $this->audit->log('banner_duplicated', 'banner', $newId, ['source_id' => $id]);

        return redirect()->to('/admin/banners')->with('success', 'Banner duplicated as draft');
    }

    public function toggleBanner(int $id)
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }

        $banner = $this->db->table('banners')->where('id', $id)->get()->getRowArray();
        if (!$banner) {
            return redirect()->to('/admin/banners')->with('error', 'Banner tidak ditemukan');
        }

        $next = (int) ($banner['is_active'] ? 0 : 1);
        $this->db->table('banners')->where('id', $id)->update([
            'is_active' => $next,
            'updated_by' => (int) session()->get('user_id'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $this->audit->log($next ? 'banner_published' : 'banner_unpublished', 'banner', $id);

        return redirect()->to('/admin/banners')->with('success', $next ? 'Banner published' : 'Banner unpublished');
    }

    public function featuredProducts()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $products = $this->db->table('products p')
            ->select('p.id, p.name, p.status, s.name as store_name')
            ->join('stores s', 's.id = p.store_id')
            ->where('p.status', 'ACTIVE')
            ->orderBy('p.name', 'ASC')
            ->get()->getResultArray();

        return view('admin/cms/featured_products', [
            'title' => 'Featured Products',
            'products' => $products,
            'selected' => array_column($this->db->table('featured_products')->where('is_active', 1)->get()->getResultArray(), 'product_id'),
        ]);
    }

    public function saveFeaturedProducts()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $selected = $this->request->getPost('product_ids') ?? [];
        $selected = array_map('intval', is_array($selected) ? $selected : []);

        $this->db->transStart();
        $this->db->table('featured_products')->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);

        $existingRows = $this->db->table('featured_products')->select('id, product_id')->get()->getResultArray();
        $existingMap = [];
        foreach ($existingRows as $row) {
            $existingMap[(int) $row['product_id']] = (int) $row['id'];
        }

        foreach ($selected as $i => $pid) {
            if (isset($existingMap[$pid])) {
                $this->db->table('featured_products')->where('id', $existingMap[$pid])->update([
                    'is_active' => 1,
                    'sort_order' => $i + 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->db->table('featured_products')->insert([
                    'product_id' => $pid,
                    'is_active' => 1,
                    'sort_order' => $i + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        $this->db->transComplete();

        $this->audit->log('featured_products_updated', 'homepage', null, ['total' => count($selected)]);
        return redirect()->to('/admin/featured-products')->with('success', 'Featured products updated');
    }

    public function featuredStores()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $stores = $this->db->table('stores')->where('status', 'ACTIVE')->orderBy('name', 'ASC')->get()->getResultArray();
        return view('admin/cms/featured_stores', [
            'title' => 'Featured Stores',
            'stores' => $stores,
            'selected' => array_column($this->db->table('featured_stores')->where('is_active', 1)->get()->getResultArray(), 'store_id'),
        ]);
    }

    public function saveFeaturedStores()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $selected = $this->request->getPost('store_ids') ?? [];
        $selected = array_map('intval', is_array($selected) ? $selected : []);

        $this->db->transStart();
        $this->db->table('featured_stores')->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);

        $existingRows = $this->db->table('featured_stores')->select('id, store_id')->get()->getResultArray();
        $existingMap = [];
        foreach ($existingRows as $row) {
            $existingMap[(int) $row['store_id']] = (int) $row['id'];
        }

        foreach ($selected as $i => $sid) {
            if (isset($existingMap[$sid])) {
                $this->db->table('featured_stores')->where('id', $existingMap[$sid])->update([
                    'is_active' => 1,
                    'sort_order' => $i + 1,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            } else {
                $this->db->table('featured_stores')->insert([
                    'store_id' => $sid,
                    'is_active' => 1,
                    'sort_order' => $i + 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
        $this->db->transComplete();

        $this->audit->log('featured_stores_updated', 'homepage', null, ['total' => count($selected)]);
        return redirect()->to('/admin/featured-stores')->with('success', 'Featured stores updated');
    }

    public function settings()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $service = new MarketplaceSettingsService();
        return view('admin/cms/settings', [
            'title' => 'Marketplace Settings',
            'settings' => $service->all([
                'app_name' => 'BersolekMart',
                'app_tagline' => 'Marketplace UMKM Kota Probolinggo',
                'default_currency' => 'IDR',
                'shipping_base_fee' => '10000',
                'minimum_order' => '0',
                'maintenance_mode' => '0',
                'contact_email' => 'support@bersolekmart.test',
            ]),
        ]);
    }

    public function saveSettings()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $service = new MarketplaceSettingsService();
        $keys = [
            'app_name',
            'app_tagline',
            'default_currency',
            'shipping_base_fee',
            'minimum_order',
            'maintenance_mode',
            'contact_email',
        ];

        foreach ($keys as $key) {
            $value = (string) ($this->request->getPost($key) ?? '');
            if ($key === 'maintenance_mode') {
                $value = $value === '1' ? '1' : '0';
            }
            $service->set($key, $value);
        }

        $this->audit->log('marketplace_settings_updated', 'marketplace_settings', null, ['keys' => $keys]);
        return redirect()->to('/admin/settings')->with('success', 'Settings updated');
    }

    private function uploadBannerFile(string $field, ?string $existingPath = null): array
    {
        $file = $this->request->getFile($field);
        if (!$file || !$file->isValid() || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return ['path' => $existingPath];
        }

        $mime = $file->getMimeType();
        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return ['error' => 'Format gambar tidak didukung. Gunakan JPG/PNG/WEBP.'];
        }

        $targetDir = rtrim(FCPATH, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'banners';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName, true);

        return ['path' => 'uploads/banners/' . $newName];
    }

    private function activeBanners(): array
    {
        return $this->db->table('banners')
            ->where('is_active', 1)
            ->groupStart()
                ->where('starts_at IS NULL', null, false)
                ->orWhere('starts_at <=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->groupStart()
                ->where('ends_at IS NULL', null, false)
                ->orWhere('ends_at >=', date('Y-m-d H:i:s'))
            ->groupEnd()
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();
    }

    private function getFeaturedProductsData(): array
    {
        return $this->db->table('featured_products fp')
            ->select('fp.*, p.name, p.slug, p.price, p.stock')
            ->join('products p', 'p.id = fp.product_id')
            ->where('fp.is_active', 1)
            ->orderBy('fp.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    private function getFeaturedStoresData(): array
    {
        return $this->db->table('featured_stores fs')
            ->select('fs.*, s.name, s.slug, s.rating_avg')
            ->join('stores s', 's.id = fs.store_id')
            ->where('fs.is_active', 1)
            ->orderBy('fs.sort_order', 'ASC')
            ->get()->getResultArray();
    }
}
