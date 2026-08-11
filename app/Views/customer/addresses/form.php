<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4" style="max-width:560px">
    <div class="bm-card p-3">
        <h1 class="h5 mb-3"><?= $address ? 'Edit' : 'Tambah' ?> Alamat</h1>
        <form method="post" action="<?= $address ? site_url('addresses/' . $address['id']) : site_url('addresses') ?>">
            <?= csrf_field() ?>
            <div class="mb-2"><label class="form-label">Label</label><input name="label" class="form-control" value="<?= esc($address['label'] ?? 'Rumah') ?>" required></div>
            <div class="mb-2"><label class="form-label">Nama Penerima</label><input name="recipient_name" class="form-control" value="<?= esc($address['recipient_name'] ?? '') ?>" required></div>
            <div class="mb-2"><label class="form-label">Telepon</label><input name="phone" class="form-control" value="<?= esc($address['phone'] ?? '') ?>" required></div>
            <div class="mb-2"><label class="form-label">Alamat</label><textarea name="address" class="form-control" required><?= esc($address['address'] ?? '') ?></textarea></div>
            <div class="row g-2">
                <div class="col-md-6"><label class="form-label">Kecamatan</label><input name="district" class="form-control" value="<?= esc($address['district'] ?? '') ?>" required></div>
                <div class="col-md-6"><label class="form-label">Kota</label><input name="city" class="form-control" value="<?= esc($address['city'] ?? 'Probolinggo') ?>"></div>
            </div>
            <div class="mb-2 mt-2"><label class="form-label">Kode Pos</label><input name="postal_code" class="form-control" value="<?= esc($address['postal_code'] ?? '') ?>"></div>

            <input type="hidden" name="latitude" id="addressLatitude" value="<?= esc($address['latitude'] ?? '') ?>">
            <input type="hidden" name="longitude" id="addressLongitude" value="<?= esc($address['longitude'] ?? '') ?>">
            <input type="hidden" name="location_accuracy" id="addressAccuracy" value="<?= esc($address['location_accuracy'] ?? '') ?>">
            <input type="hidden" name="location_recorded_at" id="addressLocationRecordedAt" value="<?= esc($address['location_recorded_at'] ?? '') ?>">

            <div class="bm-card p-3 bg-light border mb-3" id="addressGpsCard">
                <div class="fw-semibold small mb-1">📍 Lokasi Pengantaran (GPS)</div>
                <?php if (!empty($address['latitude']) && !empty($address['longitude'])): ?>
                <div class="border rounded p-2 bg-white mb-2" id="addressGpsResult">
                    <div class="small text-success fw-semibold mb-1">✅ Lokasi tersimpan</div>
                    <div class="small bm-muted">Lat: <span id="addressGpsLat"><?= esc($address['latitude']) ?></span></div>
                    <div class="small bm-muted">Lng: <span id="addressGpsLng"><?= esc($address['longitude']) ?></span></div>
                    <?php if (!empty($address['location_accuracy'])): ?>
                    <div class="small bm-muted">Akurasi: <span id="addressGpsAcc"><?= esc($address['location_accuracy']) ?></span> meter</div>
                    <?php else: ?>
                    <div class="small bm-muted d-none" id="addressGpsAccRow">Akurasi: <span id="addressGpsAcc"></span> meter</div>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <div class="border rounded p-2 bg-white mb-2 d-none" id="addressGpsResult">
                    <div class="small text-success fw-semibold mb-1">✅ Lokasi berhasil ditangkap</div>
                    <div class="small bm-muted">Lat: <span id="addressGpsLat"></span></div>
                    <div class="small bm-muted">Lng: <span id="addressGpsLng"></span></div>
                    <div class="small bm-muted d-none" id="addressGpsAccRow">Akurasi: <span id="addressGpsAcc"></span> meter</div>
                </div>
                <?php endif; ?>
                <div class="small bm-muted mb-2" id="addressLocationStatus">
                    <?php if (!empty($address['latitude']) && !empty($address['longitude'])): ?>
                        Klik tombol untuk memperbarui lokasi GPS.
                    <?php else: ?>
                        Simpan titik GPS untuk memudahkan kurir menemukan lokasi Anda.
                    <?php endif; ?>
                </div>
                <div id="addressGpsError" class="alert alert-danger small py-1 px-2 mb-2 d-none"></div>
                <button type="button" class="btn btn-outline-primary btn-sm" id="addressGpsBtn" data-address-geolocate>
                    📍 Gunakan Lokasi Saya
                </button>
            </div>

            <div class="form-check mb-3"><input type="checkbox" name="is_default" value="1" class="form-check-input" id="def" <?= !empty($address['is_default']) ? 'checked' : '' ?>><label for="def" class="form-check-label">Jadikan default</label></div>
            <button class="btn bm-btn-primary w-100">Simpan Alamat</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
