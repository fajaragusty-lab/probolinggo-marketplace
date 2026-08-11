<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h1 class="h5 mb-1"><?= esc($category['name']) ?></h1><div class="small bm-muted">Kategori marketplace</div></div>
        <a href="<?= site_url('search') ?>" class="btn btn-sm btn-outline-secondary">Lihat semua</a>
    </div>
    <?php if (empty($products ?? [])): ?>
        <div class="bm-empty">Belum ada produk aktif di kategori ini.</div>
    <?php else: ?>
        <div class="row g-2 g-md-3"><?php foreach ($products as $product): ?><div class="col-6 col-md-3"><?= view('components/product_card', ['product' => $product]) ?></div><?php endforeach; ?></div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
