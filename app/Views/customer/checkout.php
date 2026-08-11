<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3">
    <h1 class="h5 mb-3">Checkout</h1>

    <?php if (empty($addresses)): ?>
        <div class="alert alert-warning">Belum ada alamat. <a href="<?= site_url('addresses/create') ?>">Tambah alamat</a> dulu.</div>
    <?php else: ?>
    <form action="<?= site_url('checkout') ?>" method="post">
        <?= csrf_field() ?>
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h2 class="h6">Alamat Pengiriman</h2>
                <?php foreach ($addresses as $a): ?>
                <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="address_id" id="addr<?= $a['id'] ?>" value="<?= $a['id'] ?>" <?= $a['is_default'] ? 'checked' : '' ?> required>
                    <label class="form-check-label" for="addr<?= $a['id'] ?>">
                        <strong><?= esc($a['label']) ?></strong> — <?= esc($a['recipient_name']) ?><br>
                        <span class="small text-muted"><?= esc($a['address']) ?>, <?= esc($a['district']) ?>, <?= esc($a['city']) ?></span>
                    </label>
                </div>
                <?php endforeach; ?>
                <a href="<?= site_url('addresses/create') ?>" class="small">+ Alamat baru</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body">
                <h2 class="h6">Ringkasan</h2>
                <?php foreach ($items as $item): ?>
                <div class="d-flex justify-content-between small mb-1">
                    <span><?= esc($item['product_name']) ?> × <?= $item['quantity'] ?></span>
                    <span>Rp <?= number_format($item['line_total'], 0, ',', '.') ?></span>
                </div>
                <?php endforeach; ?>
                <hr>
                <div class="d-flex justify-content-between"><span>Subtotal</span><span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span></div>
                <div class="d-flex justify-content-between"><span>Ongkir</span><span>Rp <?= number_format($shippingFee, 0, ',', '.') ?></span></div>
                <div class="d-flex justify-content-between fw-bold mt-1"><span>Total</span><span>Rp <?= number_format($total, 0, ',', '.') ?></span></div>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Catatan (opsional)</label>
            <textarea name="notes" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-bm w-100 btn-lg">Buat Pesanan</button>
    </form>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
