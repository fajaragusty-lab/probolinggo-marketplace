<?php

namespace App\Controllers\Admin;

use App\Services\AdminAnalyticsService;

class StatisticsController extends BaseAdminController
{
    public function index()
    {
        $guard = $this->guard();
        if ($guard) { return $guard; }
        $analytics = new AdminAnalyticsService();
        $preset = (string) ($this->request->getGet('range') ?: 'last7');
        $range = $analytics->parseRange($preset, $this->request->getGet('from'), $this->request->getGet('to'));

        return view('admin/statistics', [
            'title' => 'Statistics',
            'preset' => $preset,
            'range' => $range,
            'kpis' => $analytics->kpis($range['from'], $range['to']),
            'ordersOverTime' => $analytics->ordersOverTime(30),
            'statusDistribution' => $analytics->statusDistribution(),
            'topProducts' => $analytics->topProducts(),
            'topStores' => $analytics->topStores(),
        ]);
    }
}
