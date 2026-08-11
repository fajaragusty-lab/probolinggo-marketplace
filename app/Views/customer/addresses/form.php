<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3" style="max-width:480px">
    <h1 class="h5 mb-3"><?= $address ? 'Edit' : 'Tambah' ?> Alamat</h1>
    <form method="post" action="<?= $address ? site_url('addresses/' . $address['id']) : site_url('addresses') ?>">
        <?= csrf_field() ?>
        <div class="mb-2"><label class="form-label">Label</label><input name="label" class="form-control" value="<?= esc($address['label'] ?? 'Rumah') ?>" required></div>
        <div class="mb-2"><label class="form-label">Nama</label><input name="recipient_name" class="form-control" value="<?= esc($address['recipient_name'] ?? '') ?>" required></div>
        <div class="mb-2"><label class="form-label">Telepon</label><input name="phone" class="form-control" value="<?= esc($address['phone'] ?? '') ?>" required></div>
        <div class="mb-2"><label class="form-label">Alamat</label><textarea name="address" class="form-control" required><?= esc($address['address'] ?? '') ?></textarea></div>
        <div class="mb-2"><label class="form-label">Kecamatan</label><input name="district" class="form-control" value="<?= esc($address['district'] ?? '') ?>" required></div>
        <div class="mb-2"><label class="form-label">Kota</label><input name="city" class="form-control" value="<?= esc($address['city'] ?? 'Probolinggo') ?>"></div>
        <div class="mb-2"><label class="form-label">Kode Pos</label><input name="postal_code" class="form-control" value="<?= esc($address['postal_code'] ?? '') ?>"></div>
        <div class="form-check mb-3"><input type="checkbox" name="is_default" value="1" class="form-check-input" id="def" <?= !empty($address['is_default']) ? 'checked' : '' ?>><label for="def" class="form-check-label">Default</label></div>
        <button class="btn btn-bm w-100">Simpan</button>
    </form>
</div>
<?= $this->endSection() ?>
