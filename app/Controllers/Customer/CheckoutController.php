<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Services\MarketplaceSettingsService;

class CheckoutController extends BaseController
{
    public function index()
    {
        $userId = (int) session()->get('user_id');
        $cartData = (new CartService())->getCart($userId);
        if (empty($cartData['items'])) {
            return redirect()->to('/cart')->with('error', 'Keranjang kosong');
        }
        $addresses = \Config\Database::connect()->table('addresses')
            ->where('user_id', $userId)->orderBy('is_default', 'DESC')->get()->getResultArray();
        $paymentMethods = \Config\Database::connect()->table('payment_methods')
            ->where('is_active', 1)
            ->orderBy('method_name', 'ASC')
            ->get()->getResultArray();
        $settings = (new MarketplaceSettingsService())->all([
            'marketplace_shipping_base_fee' => '10000',
            'checkout_payment_methods' => 'bank_transfer,qris,cod',
            'checkout_cod' => '1',
        ]);
        $allowedMethods = array_filter(array_map('trim', explode(',', (string) ($settings['checkout_payment_methods'] ?? ''))));
        if (!empty($allowedMethods)) {
            $paymentMethods = array_values(array_filter($paymentMethods, static fn (array $method) => in_array($method['method_code'], $allowedMethods, true)));
        }
        if (($settings['checkout_cod'] ?? '0') === '1' && !array_filter($paymentMethods, static fn (array $method) => $method['method_code'] === 'cod')) {
            $paymentMethods[] = [
                'method_code' => 'cod',
                'method_name' => 'Cash on Delivery (COD)',
                'provider' => 'bersolekmart',
                'config_json' => json_encode(['instruction' => 'Bayar tunai saat pesanan diterima.']),
            ];
        }
        $groupedByStore = [];
        foreach ($cartData['items'] as $item) {
            $groupedByStore[$item['store_name']][] = $item;
        }
        $shippingFee = (int) ($settings['marketplace_shipping_base_fee'] ?? 10000);
        return view('customer/checkout', [
            'items' => $cartData['items'],
            'groupedByStore' => $groupedByStore,
            'subtotal' => $cartData['subtotal'],
            'shippingFee' => $shippingFee,
            'total' => $cartData['subtotal'] + $shippingFee,
            'addresses' => $addresses,
            'paymentMethods' => $paymentMethods,
            'checkoutToken' => bin2hex(random_bytes(16)),
            'settings' => $settings,
        ]);
    }

    public function process()
    {
        $userId = (int) session()->get('user_id');
        $addressId = (int) $this->request->getPost('address_id');
        if (!$addressId) {
            return redirect()->back()->with('error', 'Pilih alamat pengiriman');
        }
        $result = (new CheckoutService())->process(
            $userId,
            $addressId,
            (string) $this->request->getPost('notes'),
            (string) $this->request->getPost('payment_method'),
            (string) $this->request->getPost('shipping_method'),
            (string) $this->request->getPost('checkout_token')
        );
        if ($result['success']) {
            return redirect()->to('/orders/' . $result['order_id'])
                ->with('success', 'Pesanan ' . $result['order_number'] . ' berhasil. Total Rp ' . number_format($result['total'], 0, ',', '.'));
        }
        return redirect()->to('/checkout')->with('error', $result['message']);
    }
}
