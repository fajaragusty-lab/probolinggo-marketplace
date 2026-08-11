<?php

namespace App\Services;

use App\Models\CartModel;
use App\Models\ProductModel;

class CheckoutService
{
    protected CartService $cartService;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->cartService  = new CartService();
        $this->productModel = model(ProductModel::class);
    }

    public function process(int $userId, int $addressId, ?string $notes = null): array
    {
        $db = \Config\Database::connect();

        $address = $db->table('addresses')
            ->where(['id' => $addressId, 'user_id' => $userId])
            ->get()->getRowArray();
        if (!$address) {
            return ['success' => false, 'message' => 'Alamat tidak valid'];
        }

        $cartData = $this->cartService->getCart($userId);
        if (empty($cartData['items'])) {
            return ['success' => false, 'message' => 'Keranjang kosong'];
        }

        $db->transStart();

        try {
            foreach ($cartData['items'] as $item) {
                $locked = $db->query(
                    'SELECT id, stock, status, price, name, store_id, weight FROM products WHERE id = ? FOR UPDATE',
                    [$item['product_id']]
                )->getRowArray();

                if (!$locked || $locked['status'] !== 'ACTIVE') {
                    throw new \RuntimeException('Produk ' . $item['product_name'] . ' tidak tersedia');
                }
                if ((int) $locked['stock'] < (int) $item['quantity']) {
                    throw new \RuntimeException('Stok ' . $item['product_name'] . ' tidak mencukupi');
                }
            }

            $shippingFee = (int) (env('SHIPPING_BASE_FEE') ?: 10000);
            $subtotal    = $cartData['subtotal'];
            $total       = $subtotal + $shippingFee;
            $orderNumber = 'BM' . date('Ymd') . strtoupper(substr(uniqid(), -6));

            $db->table('orders')->insert([
                'order_number' => $orderNumber,
                'customer_id'  => $userId,
                'address_id'   => $addressId,
                'status'       => 'PENDING_PAYMENT',
                'subtotal'     => $subtotal,
                'shipping_fee' => $shippingFee,
                'discount'     => 0,
                'total'        => $total,
                'notes'        => $notes,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
            $orderId = $db->insertID();

            $storeIds = [];
            foreach ($cartData['items'] as $item) {
                $product = $this->productModel->find($item['product_id']);
                $db->table('order_items')->insert([
                    'order_id'      => $orderId,
                    'store_id'      => $product['store_id'],
                    'product_id'    => $item['product_id'],
                    'product_name'  => $item['product_name'],
                    'product_price' => $item['price'],
                    'quantity'      => $item['quantity'],
                    'subtotal'      => (int) $item['price'] * (int) $item['quantity'],
                    'weight'        => $product['weight'] ?? 0,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
                $storeIds[$product['store_id']] = true;

                if (!$this->productModel->decreaseStock($item['product_id'], (int) $item['quantity'])) {
                    throw new \RuntimeException('Gagal mengurangi stok');
                }
            }

            $paymentNumber = 'PAY' . date('Ymd') . strtoupper(substr(uniqid(), -6));
            $db->table('payments')->insert([
                'order_id'       => $orderId,
                'payment_number' => $paymentNumber,
                'provider'       => env('PAYMENT_PROVIDER') ?: 'development',
                'amount'         => $total,
                'status'         => 'PENDING',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            $stores = $db->table('stores')->whereIn('id', array_keys($storeIds))->get()->getResultArray();
            $storeMap = [];
            foreach ($stores as $s) {
                $storeMap[$s['id']] = $s;
            }

            $deliveryAddr = $address['address'] . ', ' . $address['district'] . ', ' . $address['city'];
            foreach (array_keys($storeIds) as $sid) {
                $s = $storeMap[$sid] ?? null;
                $db->table('shipments')->insert([
                    'order_id'         => $orderId,
                    'store_id'         => $sid,
                    'shipment_number'  => 'SH' . date('Ymd') . strtoupper(substr(uniqid(), -5)),
                    'status'           => 'PENDING',
                    'delivery_fee'     => $shippingFee,
                    'pickup_address'   => $s ? ($s['address'] . ', ' . $s['district']) : null,
                    'delivery_address' => $deliveryAddr,
                    'otp_code'         => (string) random_int(100000, 999999),
                    'created_at'       => date('Y-m-d H:i:s'),
                    'updated_at'       => date('Y-m-d H:i:s'),
                ]);
            }

            $this->cartService->clear($userId);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'message' => 'Transaksi gagal'];
            }

            return [
                'success'      => true,
                'message'      => 'Pesanan berhasil dibuat',
                'order_id'     => $orderId,
                'order_number' => $orderNumber,
                'total'        => $total,
            ];
        } catch (\Throwable $e) {
            $db->transRollback();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
