<?php

namespace App\Services;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Services\MarketplaceSettingsService;

class CheckoutService
{
    protected CartService $cartService;
    protected ProductModel $productModel;

    public function __construct()
    {
        $this->cartService  = new CartService();
        $this->productModel = model(ProductModel::class);
    }

    public function process(int $userId, int $addressId, ?string $notes = null, ?string $paymentMethodCode = null, ?string $shippingMethod = null): array
    {
        $db = \Config\Database::connect();
        $settings = (new MarketplaceSettingsService())->all([
            'marketplace_shipping_base_fee' => '10000',
            'marketplace_minimum_order' => '0',
        ]);

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

        $shippingMethod = trim((string) $shippingMethod);
        if ($shippingMethod === '' || $shippingMethod !== 'bersolek_courier') {
            return ['success' => false, 'message' => 'Metode pengiriman tidak valid'];
        }

        $paymentMethodCode = trim((string) $paymentMethodCode);
        $paymentMethod = $db->table('payment_methods')
            ->where('method_code', $paymentMethodCode)
            ->where('is_active', 1)
            ->get()
            ->getRowArray();
        if (!$paymentMethod) {
            return ['success' => false, 'message' => 'Metode pembayaran tidak tersedia'];
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

            $shippingFee = (int) ($settings['marketplace_shipping_base_fee'] ?? 10000);
            $subtotal    = $cartData['subtotal'];
            $minimumOrder = (int) ($settings['marketplace_minimum_order'] ?? 0);
            if ($subtotal < $minimumOrder) {
                throw new \RuntimeException('Minimum order belum terpenuhi');
            }
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
                'provider'       => $paymentMethod['provider'] ?: 'development',
                'amount'         => $total,
                'status'         => 'PENDING',
                'metadata'       => json_encode([
                    'method_code' => $paymentMethod['method_code'],
                    'method_name' => $paymentMethod['method_name'],
                    'shipping_method' => $shippingMethod,
                    'instructions' => json_decode((string) ($paymentMethod['config_json'] ?? 'null'), true),
                ]),
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
