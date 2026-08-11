<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Services\CartService;
use App\Services\CheckoutService;

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
        $groupedByStore = [];
        foreach ($cartData['items'] as $item) {
            $groupedByStore[$item['store_name']][] = $item;
        }
        $shippingFee = (int) (env('SHIPPING_BASE_FEE') ?: 10000);
        return view('customer/checkout', [
            'items' => $cartData['items'],
            'groupedByStore' => $groupedByStore,
            'subtotal' => $cartData['subtotal'],
            'shippingFee' => $shippingFee,
            'total' => $cartData['subtotal'] + $shippingFee,
            'addresses' => $addresses,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    public function process()
    {
        $userId = (int) session()->get('user_id');
        $addressId = (int) $this->request->getPost('address_id');
        if (!$addressId) {
            return redirect()->back()->with('error', 'Pilih alamat pengiriman');
        }
        $result = (new CheckoutService())->process($userId, $addressId, $this->request->getPost('notes'));
        if ($result['success']) {
            return redirect()->to('/orders/' . $result['order_id'])
                ->with('success', 'Pesanan ' . $result['order_number'] . ' berhasil. Total Rp ' . number_format($result['total'], 0, ',', '.'));
        }
        return redirect()->to('/checkout')->with('error', $result['message']);
    }
}
