<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php
$kpiMap = [
    'gmv' => ['label' => 'GMV', 'icon' => 'bi-cash-stack', 'link' => 'admin/reports'],
    'revenue' => ['label' => 'Revenue', 'icon' => 'bi-wallet2', 'link' => 'admin/reports'],
    'total_orders' => ['label' => 'Orders', 'icon' => 'bi-receipt', 'link' => 'admin/orders'],
    'total_customers' => ['label' => 'Customers', 'icon' => 'bi-people', 'link' => 'admin/customers'],
    'total_umkm' => ['label' => 'UMKM', 'icon' => 'bi-shop', 'link' => 'admin/umkm'],
    'total_couriers' => ['label' => 'Couriers', 'icon' => 'bi-truck', 'link' => 'admin/couriers'],
    'pending_orders' => ['label' => 'Pending Orders', 'icon' => 'bi-hourglass-split', 'link' => 'admin/orders'],
    'total_products' => ['label' => 'Products', 'icon' => 'bi-box-seam', 'link' => 'admin/products'],
];
$maxOrders = max(1, ...array_map(static fn (array $row) => (int) ($row['total'] ?? 0), $ordersOverTime ?: [['total' => 0]]));
$maxRevenue = max(1, ...array_map(static fn (array $row) => (int) ($row['revenue'] ?? 0), $ordersOverTime ?: [['revenue' => 0]]));
$statusMax = max(1, ...array_map(static fn (array $row) => (int) ($row['total'] ?? 0), $statusDistribution ?: [['total' => 0]]));
?>

<div class="bm-kpi-grid mb-3">
    <?php foreach ($kpiMap as $key => $config): ?>
        <a href="<?= site_url($config['link']) ?>" class="bm-kpi bm-kpi-link">
            <div class="bm-kpi-meta">
                <div>
                    <div class="small bm-muted"><?= esc($config['label']) ?></div>
                    <div class="h5 mb-0"><?= number_format((int)($kpis[$key] ?? 0), 0, ',', '.') ?></div>
                </div>
                <div class="bm-kpi-icon"><i class="bi <?= esc($config['icon']) ?>"></i></div>
            </div>
        </a>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-3">
    <div class="col-xl-8">
        <div class="bm-card bm-chart-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h6 mb-1">Sales & Revenue Trend</h2>
                    <div class="small bm-muted">Ringkasan 14 hari terakhir dari transaksi aktual marketplace.</div>
                </div>
                <a href="<?= site_url('admin/analytics') ?>" class="btn btn-sm btn-outline-primary">Buka analytics</a>
            </div>
            <div class="bm-mini-chart">
                <?php foreach ($ordersOverTime as $row): ?>
                    <div class="bm-mini-bar-col">
                        <div class="bm-mini-bar is-secondary" style="height: <?= max(10, ((int) $row['revenue'] / $maxRevenue) * 160) ?>px"></div>
                        <div class="bm-mini-bar" style="height: <?= max(10, ((int) $row['total'] / $maxOrders) * 120) ?>px"></div>
                        <div class="bm-chart-label"><?= esc(date('d M', strtotime($row['d']))) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="bm-card bm-chart-card h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h2 class="h6 mb-1">Order Status Mix</h2>
                    <div class="small bm-muted">Distribusi status order yang sedang berjalan.</div>
                </div>
                <a href="<?= site_url('admin/orders') ?>" class="btn btn-sm btn-outline-primary">Lihat order</a>
            </div>
            <div class="bm-progress-list">
                <?php foreach ($statusDistribution as $row): ?>
                    <div class="bm-progress-row">
                        <div class="bm-progress-meta">
                            <span><?= esc($row['status']) ?></span>
                            <strong><?= (int) $row['total'] ?></strong>
                        </div>
                        <div class="bm-progress-track">
                            <div class="bm-progress-fill" style="width: <?= min(100, ((int) $row['total'] / $statusMax) * 100) ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-xl-4">
        <div class="bm-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h6 mb-0">Top Produk</h2><a href="<?= site_url('admin/products') ?>" class="small text-primary text-decoration-none">Kelola</a></div>
            <?php if (empty($topProducts)): ?><div class="bm-empty"><div class="small bm-muted">Belum ada produk terjual.</div></div><?php endif; ?>
            <table class="bm-table"><tbody><?php foreach ($topProducts as $p): ?><tr><td><div class="fw-semibold small"><?= esc($p['name']) ?></div><div class="small bm-muted"><?= esc($p['store_name']) ?></div></td><td class="text-end"><?= (int)$p['sold_count'] ?> sold</td></tr><?php endforeach; ?></tbody></table>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="bm-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h6 mb-0">Top Toko</h2><a href="<?= site_url('admin/umkm') ?>" class="small text-primary text-decoration-none">Kelola</a></div>
            <?php if (empty($topStores)): ?><div class="bm-empty"><div class="small bm-muted">Belum ada performa toko.</div></div><?php endif; ?>
            <table class="bm-table"><tbody><?php foreach ($topStores as $s): ?><tr><td><?= esc($s['name']) ?></td><td class="text-end"><?= bm_currency((int)$s['sales']) ?></td></tr><?php endforeach; ?></tbody></table>
        </div>
    </div>
    <div class="col-xl-4">
        <div class="bm-card p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2"><h2 class="h6 mb-0">Recent Activity</h2><a href="<?= site_url('admin/audit-log') ?>" class="small text-primary text-decoration-none">Audit log</a></div>
            <?php if (empty($recentActivities)): ?><div class="bm-empty py-3">Belum ada aktivitas.</div><?php endif; ?>
            <?php foreach ($recentActivities as $a): ?><div class="border-bottom py-2 small"><div class="fw-semibold"><?= esc($a['action']) ?></div><div class="bm-muted"><?= esc($a['created_at']) ?></div></div><?php endforeach; ?>
        </div>
    </div>
</div>

<div class="bm-card p-3 mt-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h2 class="h6 mb-1">Orders Requiring Action</h2>
            <div class="small bm-muted">Prioritaskan pembayaran tertunda, order baru, dan proses fulfillment.</div>
        </div>
        <a href="<?= site_url('admin/orders') ?>" class="btn btn-sm btn-outline-primary">Buka modul orders</a>
    </div>
    <?php if (empty($pendingOrders)): ?>
        <div class="bm-empty">
            <h3 class="h6 mb-1">Tidak ada order yang perlu ditindak</h3>
            <p class="small bm-muted mb-0">Saat ini semua order berada pada jalur operasional yang sehat.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="bm-table">
                <thead><tr><th>Order</th><th>Status</th><th>Total</th><th>Dibuat</th></tr></thead>
                <tbody><?php foreach ($pendingOrders as $order): ?><tr><td><?= esc($order['order_number']) ?></td><td><span class="bm-status <?= bm_status_class($order['status']) ?>"><?= esc($order['status']) ?></span></td><td><?= bm_currency((int)$order['total']) ?></td><td><?= esc($order['created_at']) ?></td></tr><?php endforeach; ?></tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
