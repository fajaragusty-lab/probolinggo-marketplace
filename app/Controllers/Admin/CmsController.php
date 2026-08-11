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
        $this->guard();
        return view('admin/cms/homepage', [
            'title' => 'Homepage CMS',
            'banners' => $this->activeBanners(),
            'featuredProducts' => $this->featuredProducts(),
            'featuredStores' => $this->featuredStores(),
        ]);
    }

    public function banners()
    {
        $this->guard();
        return view('admin/cms/banners', [
            'title' => 'Banners',
            'banners' => $this->db->table('banners')->orderBy('sort_order', 'ASC')->get()->getResultArray(),
        ]);
    }

    public function saveBanner()
    {
        $this->guard();
        $rules = [
            'title' => 'required|min_length[3]|max_length[150]',
            'cta_url' => 'permit_empty|valid_url_strict',
            'sort_order' => 'permit_empty|integer',
        ];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $id = (int) $this->request->getPost('id');
        $data = [
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'image' => $this->request->getPost('image') ?: null,
            'mobile_image' => $this->request->getPost('mobile_image') ?: null,
            'cta_label' => $this->request->getPost('cta_label') ?: null,
            'cta_url' => $this->request->getPost('cta_url') ?: null,
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

    public function featuredProducts()
    {
        $this->guard();
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
        $this->guard();
        $selected = $this->request->getPost('product_ids') ?? [];
        $selected = array_map('intval', is_array($selected) ? $selected : []);

        $this->db->transStart();
        $this->db->table('featured_products')->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);

        foreach ($selected as $i => $pid) {
            $existing = $this->db->table('featured_products')->where('product_id', $pid)->get()->getRowArray();
            if ($existing) {
                $this->db->table('featured_products')->where('id', $existing['id'])->update([
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
        $this->guard();
        $stores = $this->db->table('stores')->where('status', 'ACTIVE')->orderBy('name', 'ASC')->get()->getResultArray();
        return view('admin/cms/featured_stores', [
            'title' => 'Featured Stores',
            'stores' => $stores,
            'selected' => array_column($this->db->table('featured_stores')->where('is_active', 1)->get()->getResultArray(), 'store_id'),
        ]);
    }

    public function saveFeaturedStores()
    {
        $this->guard();
        $selected = $this->request->getPost('store_ids') ?? [];
        $selected = array_map('intval', is_array($selected) ? $selected : []);

        $this->db->transStart();
        $this->db->table('featured_stores')->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
        foreach ($selected as $i => $sid) {
            $existing = $this->db->table('featured_stores')->where('store_id', $sid)->get()->getRowArray();
            if ($existing) {
                $this->db->table('featured_stores')->where('id', $existing['id'])->update([
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
        $this->guard();
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
        $this->guard();
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
            $service->set($key, (string) ($this->request->getPost($key) ?? ''));
        }

        $this->audit->log('marketplace_settings_updated', 'marketplace_settings', null, ['keys' => $keys]);
        return redirect()->to('/admin/settings')->with('success', 'Settings updated');
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

    private function featuredProducts(): array
    {
        return $this->db->table('featured_products fp')
            ->select('fp.*, p.name, p.slug, p.price, p.stock')
            ->join('products p', 'p.id = fp.product_id')
            ->where('fp.is_active', 1)
            ->orderBy('fp.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    private function featuredStores(): array
    {
        return $this->db->table('featured_stores fs')
            ->select('fs.*, s.name, s.slug, s.rating_avg')
            ->join('stores s', 's.id = fs.store_id')
            ->where('fs.is_active', 1)
            ->orderBy('fs.sort_order', 'ASC')
            ->get()->getResultArray();
    }
}
