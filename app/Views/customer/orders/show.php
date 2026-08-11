<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3">
    <a href="<?= site_url('orders') ?>" class="btn btn-sm btn-outline-secondary mb-2">← Kembali</a>
    <h1 class="h5"><?= esc($order['order_number']) ?></h1>
    <span class="badge bg-primary mb-3"><?= esc($order['status']) ?></span>
    <div class="card border-0 shadow-sm mb-3"><div class="card-body">
        <?php foreach ($items as $item): ?>
        <div class="d-flex justify-content-between small mb-1">
            <span><?= esc($item['product_name']) ?> × <?= $item['quantity'] ?></span>
            <span>Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></span>
        </div>
        <?php endforeach; ?>
        <hr>
        <div class="d-flex justify-content-between fw-bold"><span>Total</span><span>Rp <?= number_format($order['total'], 0, ',', '.') ?></span></div>
    </div></div>
    <?php if (!empty($payment)): ?>
    <div class="card border-0 shadow-sm mb-3"><div class="card-body small">Pembayaran: <?= esc($payment['payment_number']) ?> · <?= esc($payment['status']) ?></div></div>
    <?php endif; ?>
    <?php if (!empty($shipments)): foreach ($shipments as $s): ?>
    <div class="small mb-1">Shipment <?= esc($s['shipment_number']) ?>: <?= esc($s['status']) ?></div>
    <?php endforeach; endif; ?>
</div>
<?= $this->endSection() ?>
