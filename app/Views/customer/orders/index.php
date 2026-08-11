<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3">
    <h1 class="h5 mb-3">Pesanan Saya</h1>
    <?php if (empty($orders)): ?>
        <p class="text-muted text-center py-4">Belum ada pesanan.</p>
    <?php else: foreach ($orders as $o): ?>
        <a href="<?= site_url('orders/' . $o['id']) ?>" class="card border-0 shadow-sm mb-2 text-decoration-none text-dark">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between"><strong><?= esc($o['order_number']) ?></strong><span class="badge bg-secondary"><?= esc($o['status']) ?></span></div>
                <div class="small text-muted"><?= esc($o['created_at']) ?></div>
                <div class="price">Rp <?= number_format($o['total'], 0, ',', '.') ?></div>
            </div>
        </a>
    <?php endforeach; endif; ?>
</div>
<?= $this->endSection() ?>
