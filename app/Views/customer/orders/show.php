<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <a href="<?= site_url('orders') ?>" class="btn btn-sm btn-outline-secondary mb-2"><i class="bi bi-arrow-left"></i> Kembali</a>
    <div class="bm-card p-3 mb-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div>
                <h1 class="h5 mb-1"><?= esc($order['order_number']) ?></h1>
                <div class="small bm-muted">Dibuat: <?= esc($order['created_at']) ?></div>
            </div>
            <span class="bm-status <?= bm_status_class($order['status']) ?>"><?= esc($order['status']) ?></span>
        </div>
    </div>

    <div class="bm-card p-3 mb-3">
        <h2 class="h6 mb-2">Item Pesanan</h2>
        <?php foreach ($items as $item): ?>
            <div class="d-flex justify-content-between small border-bottom py-2"><span><?= esc($item['product_name']) ?> × <?= (int)$item['quantity'] ?> <span class="bm-muted">(<?= esc($item['store_name']) ?>)</span></span><strong><?= bm_currency((int)$item['subtotal']) ?></strong></div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-between fw-bold pt-2"><span>Total</span><span><?= bm_currency((int)$order['total']) ?></span></div>
    </div>

    <?php if (!empty($payment)): ?>
        <div class="bm-card p-3 mb-3 small"><strong>Pembayaran:</strong> <?= esc($payment['payment_number']) ?> · <span class="bm-status <?= bm_status_class($payment['status']) ?>"><?= esc($payment['status']) ?></span></div>
    <?php endif; ?>

    <div class="bm-card p-3">
        <h2 class="h6 mb-2">Status Pengiriman</h2>
        <?php if (empty($shipments)): ?><div class="small bm-muted">Belum ada data pengiriman.</div><?php endif; ?>
        <?php foreach ($shipments as $s): ?>
            <div class="d-flex justify-content-between border-bottom py-2 small"><span><?= esc($s['shipment_number']) ?></span><span class="bm-status <?= bm_status_class($s['status']) ?>"><?= esc($s['status']) ?></span></div>
        <?php endforeach; ?>
    </div>
</div>
<?= $this->endSection() ?>
