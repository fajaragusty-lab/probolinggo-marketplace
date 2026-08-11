<?= $this->extend('layouts/umkm') ?>
<?= $this->section('content') ?>
<h1 class="h5 mb-1">Dashboard Seller</h1>
<div class="small bm-muted mb-3"><?= esc($umkm['business_name']) ?></div>
<div class="bm-kpi-grid mb-3">
    <div class="bm-kpi"><div class="small bm-muted">Sales Today</div><div class="h5 mb-0"><?= bm_currency((int)$salesToday) ?></div></div>
    <div class="bm-kpi"><div class="small bm-muted">Sales Month</div><div class="h5 mb-0"><?= bm_currency((int)$salesMonth) ?></div></div>
    <div class="bm-kpi"><div class="small bm-muted">Produk Aktif</div><div class="h5 mb-0"><?= (int)$productsTotal ?></div></div>
    <div class="bm-kpi"><div class="small bm-muted">Low Stock</div><div class="h5 mb-0"><?= (int)$lowStock ?></div></div>
</div>
<div class="bm-card p-3"><h2 class="h6">Pesanan Terbaru</h2><table class="bm-table"><thead><tr><th>Order</th><th>Produk</th><th>Qty</th><th>Status</th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td>#<?= (int)$o['order_id'] ?></td><td><?= esc($o['product_name']) ?></td><td><?= (int)$o['quantity'] ?></td><td><span class="bm-status <?= bm_status_class($o['status']) ?>"><?= esc($o['status']) ?></span></td></tr><?php endforeach; ?></tbody></table></div>
<?= $this->endSection() ?>
