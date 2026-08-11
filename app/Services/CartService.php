<?php

namespace App\Services;

use App\Models\CartModel;
use App\Models\ProductModel;

class CartService
{
    protected CartModel $cartModel;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->cartModel    = model(CartModel::class);
        $this->productModel = model(ProductModel::class);
    }

    public function add(int $userId, int $productId, int $quantity = 1): array
    {
        if ($quantity < 1) {
            return ['success' => false, 'message' => 'Quantity minimal 1'];
        }

        $product = $this->productModel->find($productId);
        if (!$product || $product['status'] !== 'ACTIVE') {
            return ['success' => false, 'message' => 'Produk tidak tersedia'];
        }
        $store = \Config\Database::connect()->table('stores')->select('status')->where('id', (int) $product['store_id'])->get()->getRowArray();
        if (!$store || $store['status'] !== 'ACTIVE') {
            return ['success' => false, 'message' => 'Toko sedang tidak melayani pesanan'];
        }
        if ((int) $product['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi'];
        }

        $db = \Config\Database::connect();
        $cart = $this->cartModel->getOrCreateForUser($userId);

        $existing = $db->table('cart_items')
            ->where(['cart_id' => $cart['id'], 'product_id' => $productId])
            ->get()->getRowArray();

        if ($existing) {
            $newQty = (int) $existing['quantity'] + $quantity;
            if ($newQty > (int) $product['stock']) {
                return ['success' => false, 'message' => 'Stok tidak mencukupi'];
            }
            $db->table('cart_items')->where('id', $existing['id'])->update([
                'quantity'   => $newQty,
                'price'      => $product['price'],
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('cart_items')->insert([
                'cart_id'    => $cart['id'],
                'product_id' => $productId,
                'quantity'   => $quantity,
                'price'      => $product['price'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return [
            'success'    => true,
            'message'    => 'Produk ditambahkan ke keranjang',
            'cart_count' => $this->cartModel->getItemCount($userId),
        ];
    }

    public function update(int $userId, int $itemId, int $quantity): array
    {
        $cart = $this->cartModel->where('user_id', $userId)->first();
        if (!$cart) {
            return ['success' => false, 'message' => 'Keranjang tidak ditemukan'];
        }

        $db = \Config\Database::connect();
        $item = $db->table('cart_items')
            ->where(['id' => $itemId, 'cart_id' => $cart['id']])
            ->get()->getRowArray();
        if (!$item) {
            return ['success' => false, 'message' => 'Item tidak ditemukan'];
        }

        if ($quantity < 1) {
            return $this->remove($userId, $itemId);
        }

        $product = $this->productModel->find($item['product_id']);
        if (!$product || $product['status'] !== 'ACTIVE') {
            return ['success' => false, 'message' => 'Produk tidak lagi tersedia'];
        }
        if ((int) $product['stock'] < $quantity) {
            return ['success' => false, 'message' => 'Stok tidak mencukupi'];
        }

        $db->table('cart_items')->where('id', $itemId)->update([
            'quantity'   => $quantity,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return ['success' => true, 'message' => 'Keranjang diperbarui', 'cart_count' => $this->cartModel->getItemCount($userId)];
    }

    public function remove(int $userId, int $itemId): array
    {
        $cart = $this->cartModel->where('user_id', $userId)->first();
        if (!$cart) {
            return ['success' => false, 'message' => 'Keranjang tidak ditemukan'];
        }

        $db = \Config\Database::connect();
        $deleted = $db->table('cart_items')
            ->where(['id' => $itemId, 'cart_id' => $cart['id']])
            ->delete();

        if (!$deleted) {
            return ['success' => false, 'message' => 'Item tidak ditemukan'];
        }

        return ['success' => true, 'message' => 'Item dihapus', 'cart_count' => $this->cartModel->getItemCount($userId)];
    }

    public function getCart(int $userId): array
    {
        $cart  = $this->cartModel->getOrCreateForUser($userId);
        $items = $this->cartModel->getItems($cart['id']);
        $subtotal = 0;
        foreach ($items as &$item) {
            $item['line_total'] = (int) $item['price'] * (int) $item['quantity'];
            $subtotal += $item['line_total'];
        }
        return [
            'cart'     => $cart,
            'items'    => $items,
            'subtotal' => $subtotal,
            'count'    => array_sum(array_column($items, 'quantity')),
        ];
    }

    public function clear(int $userId): void
    {
        $cart = $this->cartModel->where('user_id', $userId)->first();
        if ($cart) {
            \Config\Database::connect()->table('cart_items')->where('cart_id', $cart['id'])->delete();
        }
    }
}
