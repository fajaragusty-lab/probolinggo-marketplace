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
            <div class="text-end">
                <span class="bm-status <?= bm_status_class($order['status']) ?>"><?= esc($order['status']) ?></span>
                <div class="mt-2"><a href="<?= site_url('orders/' . (int) $order['id'] . '/tracking') ?>" class="btn btn-sm btn-outline-primary">Track Delivery</a></div>
            </div>
        </div>
    </div>

    <div class="bm-card p-3 mb-3">
        <h2 class="h6 mb-2">Item Pesanan</h2>
        <?php foreach ($items as $item): ?>
            <div class="d-flex justify-content-between small border-bottom py-2 gap-3">
                <div class="d-flex gap-2">
                    <img src="<?= esc(bm_image_url($item['primary_image'] ?? null, $item['product_name'])) ?>" alt="<?= esc($item['product_name']) ?>" style="width:56px;height:56px;object-fit:cover" class="rounded border">
                    <span><?= esc($item['product_name']) ?> × <?= (int)$item['quantity'] ?> <span class="bm-muted">(<?= esc($item['store_name']) ?>)</span></span>
                </div>
                <strong><?= bm_currency((int)$item['subtotal']) ?></strong>
            </div>
        <?php endforeach; ?>
        <div class="d-flex justify-content-between fw-bold pt-2"><span>Total</span><span><?= bm_currency((int)$order['total']) ?></span></div>
    </div>

    <?php if (!empty($payment)): ?>
        <div class="bm-card p-3 mb-3 small">
            <div class="d-flex flex-wrap justify-content-between gap-2 mb-2">
                <strong>Pembayaran <?= esc($payment['payment_number']) ?></strong>
                <span class="bm-status <?= bm_status_class($payment['status']) ?>"><?= esc($payment['status']) ?></span>
            </div>
            <div class="bm-muted mb-1">Metode: <?= esc($payment['meta']['method_name'] ?? $payment['provider']) ?></div>
            <?php if (!empty($payment['meta']['instructions']['instruction'])): ?>
                <div class="alert alert-light border mb-0"><?= esc($payment['meta']['instructions']['instruction']) ?></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="bm-card p-3">
        <h2 class="h6 mb-2">Status Pengiriman</h2>
        <?php if (empty($shipments)): ?><div class="small bm-muted">Belum ada data pengiriman.</div><?php endif; ?>
        <?php foreach ($shipments as $s): ?>
            <div class="border-bottom py-2 small">
                <div class="d-flex justify-content-between align-items-center gap-2">
                    <span><?= esc($s['shipment_number']) ?></span>
                    <span class="bm-status <?= bm_status_class($s['status']) ?>"><?= esc($s['status']) ?></span>
                </div>
                <?php if (!empty($s['courier_name'])): ?><div class="bm-muted mt-1">Kurir: <?= esc($s['courier_name']) ?> · <?= esc($s['courier_phone'] ?? '-') ?></div><?php endif; ?>
                <?php if (!empty($s['latest_tracking'])): ?><div class="bm-muted">GPS terakhir: <?= esc($s['latest_tracking']['latitude']) ?>, <?= esc($s['latest_tracking']['longitude']) ?> · <?= esc($s['latest_tracking']['recorded_at']) ?></div><?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if (in_array($order['status'], ['COMPLETED', 'FEEDBACK'], true)): ?>
        <div class="bm-card p-3 mt-3">
            <h2 class="h6 mb-3">Beri Ulasan</h2>
            <?php foreach ($items as $item): ?>
                <div class="border rounded p-3 mb-2">
                    <div class="fw-semibold small mb-2"><?= esc($item['product_name']) ?></div>
                    <?php if (in_array((int) $item['product_id'], $reviewedIds ?? [], true)): ?>
                        <div class="small text-success">Review sudah dikirim untuk produk ini.</div>
                    <?php else: ?>
                        <form method="post" action="<?= site_url('orders/' . (int) $order['id'] . '/reviews') ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <select name="rating" class="form-select form-select-sm" required>
                                        <option value="">Rating</option>
                                        <?php for ($rating = 5; $rating >= 1; $rating--): ?><option value="<?= $rating ?>">★ <?= $rating ?></option><?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-7"><input class="form-control form-control-sm" name="comment" placeholder="Bagikan pengalaman belanja Anda"></div>
                                <div class="col-md-2"><button class="btn btn-sm bm-btn-primary w-100">Kirim</button></div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
