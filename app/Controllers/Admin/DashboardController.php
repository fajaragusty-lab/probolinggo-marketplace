<?php

namespace App\Controllers\Admin;

use App\Services\AdminAnalyticsService;

class DashboardController extends BaseAdminController
{
    public function index()
    {
        $guard = $this->guard();
        if ($guard) {
            return $guard;
        }
        $analytics = new AdminAnalyticsService();
        $kpis = $analytics->kpis();

        return view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'kpis' => $kpis,
            'ordersOverTime' => $analytics->ordersOverTime(14),
            'statusDistribution' => $analytics->statusDistribution(),
            'topProducts' => $analytics->topProducts(),
            'topStores' => $analytics->topStores(),
            'pendingUmkmCount' => (int) ($kpis['pending_umkm'] ?? 0),
            'pendingCouriersCount' => (int) ($kpis['pending_couriers'] ?? 0),
            'pendingProductsCount' => (int) ($kpis['pending_moderation'] ?? 0),
            'pendingFeedbacksCount' => (int) ($kpis['pending_feedback'] ?? 0),
        ]);
    }
}
