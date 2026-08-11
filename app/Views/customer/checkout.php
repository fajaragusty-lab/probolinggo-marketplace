<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <h1 class="h5 mb-3">Checkout Pesanan</h1>

    <?php if (empty($addresses)): ?>
        <div class="bm-empty"><h2 class="h6 mb-1">Alamat belum tersedia</h2><p class="small bm-muted mb-3">Tambahkan alamat untuk melanjutkan checkout.</p><a href="<?= site_url('addresses/create') ?>" class="btn bm-btn-primary">Tambah Alamat</a></div>
    <?php else: ?>
    <form action="<?= site_url('checkout') ?>" method="post">
        <?= csrf_field() ?>
        <input type="hidden" name="checkout_token" value="<?= esc($checkoutToken ?? '') ?>">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="bm-card p-3 mb-3">
                    <h2 class="h6 mb-2">Pilih Alamat Pengiriman</h2>
                    <?php foreach ($addresses as $a): ?>
                        <label class="d-flex gap-2 border rounded p-2 mb-2 checkout-address-option" data-lat="<?= esc($a['latitude'] ?? '') ?>" data-lng="<?= esc($a['longitude'] ?? '') ?>" data-acc="<?= esc($a['location_accuracy'] ?? '') ?>">
                            <input type="radio" name="address_id" value="<?= (int)$a['id'] ?>" <?= $a['is_default'] ? 'checked' : '' ?> required class="checkout-addr-radio">
                            <div class="flex-grow-1">
                                <div class="fw-semibold small"><?= esc($a['label']) ?> — <?= esc($a['recipient_name']) ?></div>
                                <div class="small bm-muted"><?= esc($a['address']) ?>, <?= esc($a['district']) ?>, <?= esc($a['city']) ?> <?= esc($a['postal_code'] ?? '') ?></div>
                                <?php if (!empty($a['latitude']) && !empty($a['longitude'])): ?><div class="small text-success">✅ GPS: <?= esc(number_format((float)$a['latitude'], 6)) ?>, <?= esc(number_format((float)$a['longitude'], 6)) ?><?= !empty($a['location_accuracy']) ? ' · Akurasi: ' . esc(number_format((float)$a['location_accuracy'], 1)) . 'm' : '' ?></div><?php else: ?><div class="small text-warning">⚠️ Belum ada GPS — <a href="<?= site_url('addresses/' . (int)$a['id'] . '/edit') ?>">Tambah GPS</a></div><?php endif; ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                    <div id="checkoutGpsCard" class="border rounded p-2 mb-2 bg-light d-none">
                        <div class="fw-semibold small mb-1">📍 Koordinat Tujuan</div>
                        <div class="small bm-muted">Lat: <span id="checkoutGpsLat">—</span></div>
                        <div class="small bm-muted">Lng: <span id="checkoutGpsLng">—</span></div>
                        <div class="small bm-muted" id="checkoutGpsAccRow">Akurasi: <span id="checkoutGpsAcc">—</span> m</div>
                    </div>
                    <div id="checkoutNoGpsWarning" class="alert alert-warning small py-1 px-2 mb-2 d-none">
                        ⚠️ Alamat ini belum memiliki GPS. Tambahkan GPS di halaman edit alamat agar kurir dapat menemukan lokasi Anda.
                    </div>
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
                        <select class="form-select" name="shipping_method">
                            <option value="bersolek_courier">Kurir BersolekMart (Ongkir tetap)</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small bm-muted">Metode Pembayaran</label>
                        <select class="form-select" name="payment_method" required>
                            <?php foreach (($paymentMethods ?? []) as $m): ?><option value="<?= esc($m['method_code']) ?>"><?= esc($m['method_name']) ?></option><?php endforeach; ?>
                        </select>
                    </div>
                    <?php if (($settings['checkout_cod'] ?? '0') === '1'): ?>
                        <div class="alert alert-light border small mb-2">COD aktif. Pesanan COD langsung diteruskan ke UMKM dan kurir, tetapi pembayaran dicatat lunas saat pesanan selesai diantar.</div>
                    <?php endif; ?>
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
<?= $this->section('scripts') ?>
<script>
(function () {
    const radios = document.querySelectorAll('.checkout-addr-radio');
    const gpsCard = document.getElementById('checkoutGpsCard');
    const noGpsWarn = document.getElementById('checkoutNoGpsWarning');
    const gpsLat = document.getElementById('checkoutGpsLat');
    const gpsLng = document.getElementById('checkoutGpsLng');
    const gpsAcc = document.getElementById('checkoutGpsAcc');
    const gpsAccRow = document.getElementById('checkoutGpsAccRow');

    function updateGpsCard() {
        const checked = document.querySelector('.checkout-addr-radio:checked');
        if (!checked) return;
        const label = checked.closest('.checkout-address-option');
        const lat = label ? label.dataset.lat : '';
        const lng = label ? label.dataset.lng : '';
        const acc = label ? label.dataset.acc : '';
        if (lat && lng) {
            gpsLat.textContent = lat;
            gpsLng.textContent = lng;
            if (acc) {
                gpsAcc.textContent = acc;
                gpsAccRow.classList.remove('d-none');
            } else {
                gpsAccRow.classList.add('d-none');
            }
            gpsCard.classList.remove('d-none');
            noGpsWarn.classList.add('d-none');
        } else {
            gpsCard.classList.add('d-none');
            noGpsWarn.classList.remove('d-none');
        }
    }

    radios.forEach(r => r.addEventListener('change', updateGpsCard));
    updateGpsCard();
})();
</script>
<?= $this->endSection() ?>
