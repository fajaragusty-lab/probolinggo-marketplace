<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-kpi-grid mb-3">
    <?php foreach (['gmv'=>'GMV','revenue'=>'Revenue','total_orders'=>'Orders','total_customers'=>'Customers','total_umkm'=>'UMKM','total_couriers'=>'Couriers','pending_orders'=>'Pending Orders','total_products'=>'Products'] as $key => $label): ?>
        <div class="bm-kpi"><div class="small bm-muted"><?= esc($label) ?></div><div class="h5 mb-0"><?= number_format((int)($kpis[$key] ?? 0),0,',','.') ?></div></div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="bm-card p-3 h-100">
            <h2 class="h6 mb-2">Status Order</h2>
            <table class="bm-table"><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody><?php foreach ($statusDistribution as $row): ?><tr><td><?= esc($row['status']) ?></td><td><?= (int)$row['total'] ?></td></tr><?php endforeach; ?></tbody></table>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="bm-card p-3 h-100">
            <h2 class="h6 mb-2">Order Terbaru Butuh Aksi</h2>
            <table class="bm-table"><thead><tr><th>Order</th><th>Status</th><th>Total</th></tr></thead><tbody><?php foreach ($pendingOrders as $order): ?><tr><td><?= esc($order['order_number']) ?></td><td><span class="bm-status <?= bm_status_class($order['status']) ?>"><?= esc($order['status']) ?></span></td><td><?= bm_currency((int)$order['total']) ?></td></tr><?php endforeach; ?></tbody></table>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4"><div class="bm-card p-3 h-100"><h2 class="h6">Top Produk</h2><table class="bm-table"><tbody><?php foreach ($topProducts as $p): ?><tr><td><?= esc($p['name']) ?></td><td class="text-end"><?= (int)$p['sold_count'] ?> sold</td></tr><?php endforeach; ?></tbody></table></div></div>
    <div class="col-lg-4"><div class="bm-card p-3 h-100"><h2 class="h6">Top Toko</h2><table class="bm-table"><tbody><?php foreach ($topStores as $s): ?><tr><td><?= esc($s['name']) ?></td><td class="text-end"><?= bm_currency((int)$s['sales']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
    <div class="col-lg-4"><div class="bm-card p-3 h-100"><h2 class="h6">Recent Activity</h2><?php if (empty($recentActivities)): ?><div class="small bm-muted">Belum ada aktivitas.</div><?php endif; ?><?php foreach ($recentActivities as $a): ?><div class="border-bottom py-2 small"><div class="fw-semibold"><?= esc($a['action']) ?></div><div class="bm-muted"><?= esc($a['created_at']) ?></div></div><?php endforeach; ?></div></div>
</div>
<?= $this->endSection() ?>
