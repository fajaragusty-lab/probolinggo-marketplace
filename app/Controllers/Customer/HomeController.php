<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use App\Models\CartModel;

class HomeController extends BaseController
{
    public function index()
    {
        $productModel = model(ProductModel::class);
        $db = \Config\Database::connect();

        $categories = $db->table('categories')->where('is_active', 1)->orderBy('sort_order')->get()->getResultArray();
        $featured   = $productModel->getActiveWithStore(['featured' => 1], 8);
        $latest     = $productModel->getActiveWithStore([], 12);
        $stores     = $db->table('stores')->where('status', 'ACTIVE')->orderBy('rating_avg', 'DESC')->limit(6)->get()->getResultArray();

        $cartCount = 0;
        if (session()->get('user_id')) {
            $cartCount = model(CartModel::class)->getItemCount((int) session()->get('user_id'));
        }

        return view('customer/home', compact('categories', 'featured', 'latest', 'stores', 'cartCount'));
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
        return view('customer/product', ['product' => $product]);
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
