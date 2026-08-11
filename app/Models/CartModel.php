<?php

namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table         = 'carts';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id'];
    protected $useTimestamps = true;

    public function getOrCreateForUser(int $userId): array
    {
        $cart = $this->where('user_id', $userId)->first();
        if ($cart) {
            return $cart;
        }
        $id = $this->insert(['user_id' => $userId]);
        return $this->find($id);
    }

    public function getItems(int $cartId): array
    {
        return $this->db->table('cart_items ci')
            ->select("ci.*, p.name as product_name, p.slug as product_slug, p.stock, p.status as product_status, s.name as store_name, (SELECT file_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.is_primary DESC, pi.id ASC LIMIT 1) as primary_image")
            ->join('products p', 'p.id = ci.product_id')
            ->join('stores s', 's.id = p.store_id')
            ->where('ci.cart_id', $cartId)
            ->get()->getResultArray();
    }

    public function getItemCount(int $userId): int
    {
        $cart = $this->where('user_id', $userId)->first();
        if (!$cart) {
            return 0;
        }
        $row = $this->db->table('cart_items')
            ->selectSum('quantity')
            ->where('cart_id', $cart['id'])
            ->get()->getRowArray();
        return (int) ($row['quantity'] ?? 0);
    }
}
