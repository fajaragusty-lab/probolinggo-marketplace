<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <h1 class="h5 mb-3">Review Saya</h1>
    <?php if (empty($reviews)): ?>
        <div class="bm-empty"><h2 class="h6 mb-1">Belum ada review</h2><p class="small bm-muted mb-0">Review dari pesanan selesai akan muncul di halaman ini.</p></div>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="bm-card p-3 mb-2">
                <div class="d-flex justify-content-between gap-2">
                    <div>
                        <a href="<?= site_url('product/' . $review['product_slug']) ?>" class="fw-semibold text-decoration-none"><?= esc($review['product_name']) ?></a>
                        <div class="small bm-muted"><?= esc($review['store_name']) ?></div>
                    </div>
                    <span class="bm-status <?= bm_status_class($review['status']) ?>"><?= esc($review['status']) ?></span>
                </div>
                <div class="small mt-2">★ <?= (int) $review['rating'] ?></div>
                <div class="small bm-muted"><?= esc($review['comment'] ?: '-') ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
