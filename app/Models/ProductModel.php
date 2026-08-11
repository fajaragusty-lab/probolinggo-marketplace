<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table            = 'products';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'store_id', 'category_id', 'name', 'slug', 'description',
        'price', 'stock', 'weight', 'status', 'is_featured',
        'rating_avg', 'rating_count', 'sold_count',
    ];
    protected $useTimestamps = true;

    public function getActiveWithStore(array $filters = [], int $perPage = 12)
    {
        $b = $this->select('products.*, stores.name as store_name, stores.slug as store_slug, categories.name as category_name')
            ->join('stores', 'stores.id = products.store_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.status', 'ACTIVE')
            ->where('stores.status', 'ACTIVE');

        if (!empty($filters['q'])) {
            $q = $filters['q'];
            $b->groupStart()->like('products.name', $q)->orLike('stores.name', $q)->groupEnd();
        }
        if (!empty($filters['category_id'])) {
            $b->where('products.category_id', $filters['category_id']);
        }
        if (!empty($filters['store_id'])) {
            $b->where('products.store_id', $filters['store_id']);
        }
        if (!empty($filters['featured'])) {
            $b->where('products.is_featured', 1);
        }

        return $b->orderBy('products.created_at', 'DESC')->paginate($perPage);
    }

    public function findBySlug(string $slug): ?array
    {
        $p = $this->select('products.*, stores.name as store_name, stores.slug as store_slug, categories.name as category_name')
            ->join('stores', 'stores.id = products.store_id')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.slug', $slug)
            ->where('products.status', 'ACTIVE')
            ->first();
        if (!$p) {
            return null;
        }
        $p['images'] = $this->db->table('product_images')
            ->where('product_id', $p['id'])
            ->orderBy('is_primary', 'DESC')
            ->get()->getResultArray();
        $p['primary_image'] = $p['images'][0]['file_path'] ?? null;
        return $p;
    }

    public function decreaseStock(int $productId, int $qty): bool
    {
        return $this->db->table('products')
            ->where('id', $productId)
            ->where('stock >=', $qty)
            ->set('stock', "stock - {$qty}", false)
            ->set('sold_count', "sold_count + {$qty}", false)
            ->update();
    }
}
