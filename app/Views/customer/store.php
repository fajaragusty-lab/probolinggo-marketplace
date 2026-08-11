<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <div class="bm-card p-3 mb-3">
        <div class="d-flex gap-3 align-items-center">
            <img class="bm-avatar" src="<?= esc(bm_image_url($store['logo'] ?? null, $store['name'])) ?>" alt="<?= esc($store['name']) ?>">
            <div>
                <h1 class="h5 mb-1"><?= esc($store['name']) ?></h1>
                <div class="small bm-muted"><?= esc($store['district'] . ', ' . $store['city']) ?> · ★ <?= number_format((float)$store['rating_avg'], 1) ?></div>
            </div>
        </div>
    </div>

    <?php if (empty($products ?? [])): ?>
        <div class="bm-empty">Belum ada produk aktif di toko ini.</div>
    <?php else: ?>
        <div class="row g-2 g-md-3"><?php foreach ($products as $product): ?><div class="col-6 col-md-3"><?= view('components/product_card', ['product' => $product]) ?></div><?php endforeach; ?></div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
