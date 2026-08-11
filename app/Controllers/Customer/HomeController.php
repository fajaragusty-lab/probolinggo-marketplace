<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\CartModel;
use App\Models\ProductModel;
use App\Services\MarketplaceSettingsService;

class HomeController extends BaseController
{
    public function index()
    {
        $productModel = model(ProductModel::class);
        $db = \Config\Database::connect();

        $categories = $db->table('categories')->where('is_active', 1)->orderBy('sort_order')->get()->getResultArray();

        $featured = $db->table('featured_products fp')
            ->select('p.*, s.name as store_name, s.slug as store_slug, c.name as category_name')
            ->join('products p', 'p.id = fp.product_id')
            ->join('stores s', 's.id = p.store_id')
            ->join('categories c', 'c.id = p.category_id')
            ->where('fp.is_active', 1)
            ->where('p.status', 'ACTIVE')
            ->where('s.status', 'ACTIVE')
            ->orderBy('fp.sort_order', 'ASC')
            ->limit(8)
            ->get()->getResultArray();

        if (empty($featured)) {
            $featured = $productModel->getActiveWithStore(['featured' => 1], 8);
        }

        $latest = $productModel->getActiveWithStore([], 12);
        $trending = $db->table('products p')
            ->select("p.*, s.name as store_name, s.slug as store_slug, c.name as category_name, (SELECT file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
            ->join('stores s', 's.id = p.store_id')
            ->join('categories c', 'c.id = p.category_id')
            ->where('p.status', 'ACTIVE')
            ->where('s.status', 'ACTIVE')
            ->orderBy('p.sold_count', 'DESC')
            ->limit(8)
            ->get()->getResultArray();
        $popular = $db->table('products p')
            ->select("p.*, s.name as store_name, s.slug as store_slug, c.name as category_name, (SELECT file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
            ->join('stores s', 's.id = p.store_id')
            ->join('categories c', 'c.id = p.category_id')
            ->where('p.status', 'ACTIVE')
            ->where('s.status', 'ACTIVE')
            ->orderBy('p.rating_avg', 'DESC')
            ->limit(8)
            ->get()->getResultArray();
        $recommended = $db->table('products p')
            ->select("p.*, s.name as store_name, s.slug as store_slug, c.name as category_name, (SELECT file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
            ->join('stores s', 's.id = p.store_id')
            ->join('categories c', 'c.id = p.category_id')
            ->where('p.status', 'ACTIVE')
            ->where('s.status', 'ACTIVE')
            ->orderBy('RAND()')
            ->limit(8)
            ->get()->getResultArray();
        $stores = $db->table('featured_stores fs')
            ->select('s.*')
            ->join('stores s', 's.id = fs.store_id')
            ->where('fs.is_active', 1)
            ->where('s.status', 'ACTIVE')
            ->orderBy('fs.sort_order', 'ASC')
            ->limit(6)
            ->get()->getResultArray();

        if (empty($stores)) {
            $stores = $db->table('stores')->where('status', 'ACTIVE')->orderBy('rating_avg', 'DESC')->limit(6)->get()->getResultArray();
        }

        $banners = $db->table('banners')
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

        $cartCount = 0;
        if (session()->get('user_id')) {
            $cartCount = model(CartModel::class)->getItemCount((int) session()->get('user_id'));
        }

        $settings = (new MarketplaceSettingsService())->all([
            'app_name' => 'BersolekMart',
            'app_tagline' => 'Marketplace UMKM Kota Probolinggo',
        ]);

        return view('customer/home', compact('categories', 'featured', 'latest', 'stores', 'cartCount', 'banners', 'settings', 'trending', 'popular', 'recommended'));
    }

    public function search()
    {
        $q = $this->request->getGet('q');
        $categoryId = $this->request->getGet('category');
        $productModel = model(ProductModel::class);
        $filters = [];
        if ($q) {
            $filters['q'] = $q;
        }
        if ($categoryId) {
            $filters['category_id'] = $categoryId;
        }
        $products = $productModel->getActiveWithStore($filters, 20);
        $categories = \Config\Database::connect()->table('categories')->where('is_active', 1)->get()->getResultArray();
        return view('customer/search', [
            'products' => $products,
            'pager' => $productModel->pager,
            'q' => $q,
            'categories' => $categories,
            'categoryId' => $categoryId,
        ]);
    }

    public function categories()
    {
        return $this->search();
    }

    public function category(string $slug)
    {
        $db = \Config\Database::connect();
        $category = $db->table('categories')->where('slug', $slug)->where('is_active', 1)->get()->getRowArray();
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $productModel = model(ProductModel::class);
        $products = $productModel->getActiveWithStore(['category_id' => $category['id']], 20);
        return view('customer/category', [
            'category' => $category,
            'products' => $products,
            'pager' => $productModel->pager,
        ]);
    }

    public function product(string $slug)
    {
        $product = model(ProductModel::class)->findBySlug($slug);
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $db = \Config\Database::connect();
        $reviews = $db->table('feedbacks f')
            ->select('f.rating, f.comment, f.created_at, u.name as customer_name')
            ->join('users u', 'u.id = f.customer_id')
            ->where('f.product_id', $product['id'])
            ->where('f.status', 'APPROVED')
            ->orderBy('f.created_at', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $related = $db->table('products p')
            ->select("p.*, s.name as store_name, s.slug as store_slug, c.name as category_name, (SELECT file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
            ->join('stores s', 's.id = p.store_id')
            ->join('categories c', 'c.id = p.category_id')
            ->where('p.category_id', $product['category_id'])
            ->where('p.id !=', $product['id'])
            ->where('p.status', 'ACTIVE')
            ->where('s.status', 'ACTIVE')
            ->orderBy('p.sold_count', 'DESC')
            ->limit(8)
            ->get()->getResultArray();

        return view('customer/product', ['product' => $product, 'reviews' => $reviews, 'related' => $related]);
    }

    public function store(string $slug)
    {
        $db = \Config\Database::connect();
        $store = $db->table('stores')->where('slug', $slug)->where('status', 'ACTIVE')->get()->getRowArray();
        if (!$store) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $productModel = model(ProductModel::class);
        $products = $productModel->getActiveWithStore(['store_id' => $store['id']], 20);
        return view('customer/store', [
            'store' => $store,
            'products' => $products,
            'pager' => $productModel->pager,
        ]);
    }
}
