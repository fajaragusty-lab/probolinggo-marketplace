<?= $this->extend('layouts/umkm') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 mb-0">UMKM Dashboard</h1><a href="<?= site_url('logout') ?>" class="btn btn-outline-secondary btn-sm">Logout</a></div>
<p class="text-muted small mb-3"><?= esc($umkm['business_name']) ?></p>
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><small>Sales Today</small><div class="fw-bold">Rp <?= number_format($salesToday,0,',','.') ?></div></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><small>Sales Month</small><div class="fw-bold">Rp <?= number_format($salesMonth,0,',','.') ?></div></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><small>Products</small><div class="fw-bold"><?= (int)$productsTotal ?></div></div></div></div>
    <div class="col-6 col-md-3"><div class="card"><div class="card-body"><small>Low Stock</small><div class="fw-bold"><?= (int)$lowStock ?></div></div></div></div>
</div>
<div class="card"><div class="card-header">Recent Orders</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Order</th><th>Product</th><th>Qty</th><th>Status</th></tr></thead><tbody><?php foreach($orders as $o): ?><tr><td>#<?= (int)$o['order_id'] ?></td><td><?= esc($o['product_name']) ?></td><td><?= (int)$o['quantity'] ?></td><td><?= esc($o['status']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<?= $this->endSection() ?>
