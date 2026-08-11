<?php

namespace App\Controllers\Admin;

use App\Services\AdminAnalyticsService;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        $this->guard();
        $analytics = new AdminAnalyticsService();

        return view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'kpis' => $analytics->kpis(),
            'ordersOverTime' => $analytics->ordersOverTime(14),
            'statusDistribution' => $analytics->statusDistribution(),
            'topProducts' => $analytics->topProducts(),
            'topStores' => $analytics->topStores(),
            'pendingUmkm' => \Config\Database::connect()->table('umkms')->where('verification_status', 'PENDING')->get()->getResultArray(),
            'pendingCouriers' => \Config\Database::connect()->table('couriers')->where('verification_status', 'PENDING')->get()->getResultArray(),
            'pendingProducts' => \Config\Database::connect()->table('products')->where('status', 'PENDING')->limit(10)->get()->getResultArray(),
            'pendingFeedbacks' => \Config\Database::connect()->table('feedbacks')->where('status', 'PENDING')->limit(10)->get()->getResultArray(),
        ]);
    }
}
