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
            <input type="hidden" name="location_recorded_at" id="addressLocationRecordedAt" value="<?= esc($address['location_recorded_at'] ?? '') ?>">
            <div class="bm-card p-3 bg-light border mb-3">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <div class="fw-semibold small">Lokasi pengantaran</div>
                        <div class="small bm-muted" id="addressLocationStatus">
                            <?php if (!empty($address['latitude']) && !empty($address['longitude'])): ?>
                                GPS tersimpan di alamat ini.
                            <?php else: ?>
                                Simpan titik GPS untuk memudahkan kurir menemukan lokasi.
                            <?php endif; ?>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-address-geolocate>Gunakan GPS</button>
                </div>
            </div>
            <div class="form-check mb-3"><input type="checkbox" name="is_default" value="1" class="form-check-input" id="def" <?= !empty($address['is_default']) ? 'checked' : '' ?>><label for="def" class="form-check-label">Jadikan default</label></div>
            <button class="btn bm-btn-primary w-100">Simpan Alamat</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
