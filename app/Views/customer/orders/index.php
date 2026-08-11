<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <h1 class="h5 mb-3">Pesanan Saya</h1>
    <?php if (empty($orders)): ?>
        <div class="bm-empty"><h2 class="h6 mb-1">Belum ada pesanan</h2><p class="small bm-muted">Pesananmu akan tampil di sini setelah checkout.</p></div>
    <?php else: ?>
        <?php foreach ($orders as $o): ?>
            <a href="<?= site_url('orders/' . $o['id']) ?>" class="bm-card p-3 d-block mb-2 text-decoration-none">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-semibold"><?= esc($o['order_number']) ?></div>
                        <div class="small bm-muted"><?= esc($o['created_at']) ?></div>
                    </div>
                    <span class="bm-status <?= bm_status_class($o['status']) ?>"><?= esc($o['status']) ?></span>
                </div>
                <div class="bm-price mt-2"><?= bm_currency((int)$o['total']) ?></div>
                <div class="small text-primary mt-2">Lihat detail & tracking</div>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
