<?= $this->extend('layouts/umkm') ?>
<?= $this->section('content') ?>
<h1 class="h5 mb-3">Laporan Penjualan</h1>
<div class="bm-kpi-grid mb-3"><div class="bm-kpi"><div class="small bm-muted">Sales Today</div><div class="h5 mb-0"><?= bm_currency((int)$salesToday) ?></div></div><div class="bm-kpi"><div class="small bm-muted">Sales This Month</div><div class="h5 mb-0"><?= bm_currency((int)$salesMonth) ?></div></div></div>
<div class="bm-card p-3"><h2 class="h6">Top Produk</h2><table class="bm-table"><thead><tr><th>Produk</th><th>Qty</th><th>Revenue</th></tr></thead><tbody><?php foreach($topProducts as $p): ?><tr><td><?= esc($p['product_name']) ?></td><td><?= (int)$p['qty'] ?></td><td><?= bm_currency((int)$p['sales']) ?></td></tr><?php endforeach; ?></tbody></table></div>
<?= $this->endSection() ?>
