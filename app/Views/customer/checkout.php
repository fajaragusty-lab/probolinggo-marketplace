<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <h1 class="h5 mb-3">Checkout Pesanan</h1>

    <?php if (empty($addresses)): ?>
        <div class="bm-empty"><h2 class="h6 mb-1">Alamat belum tersedia</h2><p class="small bm-muted mb-3">Tambahkan alamat untuk melanjutkan checkout.</p><a href="<?= site_url('addresses/create') ?>" class="btn bm-btn-primary">Tambah Alamat</a></div>
    <?php else: ?>
    <form action="<?= site_url('checkout') ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="bm-card p-3 mb-3">
                    <h2 class="h6 mb-2">Pilih Alamat Pengiriman</h2>
                    <?php foreach ($addresses as $a): ?>
                        <label class="d-flex gap-2 border rounded p-2 mb-2">
                            <input type="radio" name="address_id" value="<?= (int)$a['id'] ?>" <?= $a['is_default'] ? 'checked' : '' ?> required>
                            <div>
                                <div class="fw-semibold small"><?= esc($a['label']) ?> — <?= esc($a['recipient_name']) ?></div>
                                <div class="small bm-muted"><?= esc($a['address']) ?>, <?= esc($a['district']) ?>, <?= esc($a['city']) ?> <?= esc($a['postal_code'] ?? '') ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                    <a href="<?= site_url('addresses/create') ?>" class="small">+ Tambah alamat baru</a>
                </div>

                <div class="bm-card p-3 mb-3">
                    <h2 class="h6 mb-2">Ringkasan Produk per Toko</h2>
                    <?php foreach (($groupedByStore ?? []) as $storeName => $storeItems): ?>
                        <div class="border rounded p-2 mb-2">
                            <div class="fw-semibold small mb-1"><?= esc($storeName) ?></div>
                            <?php foreach ($storeItems as $item): ?>
                                <div class="d-flex justify-content-between small"><span><?= esc($item['product_name']) ?> × <?= (int)$item['quantity'] ?></span><span><?= bm_currency((int)$item['line_total']) ?></span></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="bm-card p-3">
                    <h2 class="h6 mb-2">Pengiriman & Pembayaran</h2>
                    <div class="mb-2">
                        <label class="form-label small bm-muted">Metode Pengiriman</label>
                        <select class="form-select" disabled><option>Kurir BersolekMart (Ongkir tetap)</option></select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small bm-muted">Metode Pembayaran</label>
                        <select class="form-select" disabled>
                            <?php foreach (($paymentMethods ?? []) as $m): ?><option><?= esc($m['method_name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label small bm-muted">Catatan Pesanan</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: antar sore hari"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="bm-card p-3 position-sticky" style="top:90px">
                    <h2 class="h6 mb-2">Total Pembayaran</h2>
                    <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><span><?= bm_currency((int)$subtotal) ?></span></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Diskon</span><span><?= bm_currency(0) ?></span></div>
                    <div class="d-flex justify-content-between small mb-2"><span>Ongkir</span><span><?= bm_currency((int)$shippingFee) ?></span></div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold mb-3"><span>Total</span><span><?= bm_currency((int)$total) ?></span></div>
                    <button class="btn bm-btn-primary w-100">Konfirmasi Pesanan</button>
                </div>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
