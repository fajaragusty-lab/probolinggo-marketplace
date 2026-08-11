<?php

namespace App\Controllers\Customer;

use App\Controllers\BaseController;
use App\Services\CartService;

class CartController extends BaseController
{
    protected CartService $cartService;

    public function __construct()
    {
        $this->cartService = new CartService();
    }

    public function index()
    {
        $data = $this->cartService->getCart((int) session()->get('user_id'));
        return view('customer/cart', $data);
    }

    public function add()
    {
        $userId = (int) session()->get('user_id');
        $productId = (int) $this->request->getPost('product_id');
        $quantity = max(1, (int) ($this->request->getPost('quantity') ?: 1));
        $result = $this->cartService->add($userId, $productId, $quantity);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }
        if ($result['success'] && $this->request->getPost('buy_now')) {
            return redirect()->to('/checkout')->with('success', $result['message']);
        }
        return $result['success']
            ? redirect()->back()->with('success', $result['message'])
            : redirect()->back()->with('error', $result['message']);
    }

    public function update()
    {
        $result = $this->cartService->update(
            (int) session()->get('user_id'),
            (int) $this->request->getPost('item_id'),
            (int) $this->request->getPost('quantity')
        );
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }
        return redirect()->to('/cart')->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function remove()
    {
        $result = $this->cartService->remove(
            (int) session()->get('user_id'),
            (int) $this->request->getPost('item_id')
        );
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($result);
        }
        return redirect()->to('/cart')->with($result['success'] ? 'success' : 'error', $result['message']);
    }
}
