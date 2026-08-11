<?= $this->extend('layouts/umkm') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3">
    <h1 class="h5 mb-3">Profil Toko</h1>
    <form method="post" action="<?= site_url('umkm/store') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="bm-form-grid">
            <div class="bm-col-6"><label class="form-label">Nama Toko</label><input class="form-control" name="name" value="<?= esc($store['name'] ?? '') ?>"></div>
            <div class="bm-col-6"><label class="form-label">Status Operasional</label><select name="status" class="form-select"><option value="ACTIVE" <?= ($store['status'] ?? '') === 'ACTIVE' ? 'selected' : '' ?>>Active</option><option value="INACTIVE" <?= ($store['status'] ?? '') === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option><option value="SUSPENDED" <?= ($store['status'] ?? '') === 'SUSPENDED' ? 'selected' : '' ?>>Suspended</option></select></div>
            <div class="bm-col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" rows="3" name="description"><?= esc($store['description'] ?? '') ?></textarea></div>
            <div class="bm-col-6"><label class="form-label">Alamat</label><textarea class="form-control" rows="2" name="address"><?= esc($store['address'] ?? '') ?></textarea></div>
            <div class="bm-col-3"><label class="form-label">Kecamatan</label><input class="form-control" name="district" value="<?= esc($store['district'] ?? '') ?>"></div>
            <div class="bm-col-3"><label class="form-label">Kota</label><input class="form-control" name="city" value="<?= esc($store['city'] ?? '') ?>"></div>
            <div class="bm-col-6"><label class="form-label">Logo</label><input class="form-control" type="file" name="logo_file" accept="image/*"><?php if (!empty($store['logo'])): ?><img src="<?= esc(bm_image_url($store['logo'], $store['name'])) ?>" class="mt-2 rounded border" style="width:84px;height:84px;object-fit:cover"><?php endif; ?></div>
            <div class="bm-col-6"><label class="form-label">Cover Image</label><input class="form-control" type="file" name="cover_file" accept="image/*"><?php if (!empty($store['cover_image'])): ?><img src="<?= esc(bm_image_url($store['cover_image'], $store['name'])) ?>" class="mt-2 rounded border" style="width:100%;max-width:260px;height:84px;object-fit:cover"><?php endif; ?></div>
        </div>
        <button class="btn bm-btn-primary mt-3">Simpan Profil Toko</button>
    </form>
</div>
<?= $this->endSection() ?>
