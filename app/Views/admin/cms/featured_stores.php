<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3">
    <form method="post" action="<?= site_url('admin/featured-stores') ?>">
        <?= csrf_field() ?>
        <h2 class="h6 mb-3">Kelola Toko Unggulan Homepage</h2>
        <div class="row g-2">
            <?php foreach ($stores as $s): ?>
                <div class="col-md-6">
                    <label class="d-flex gap-2 align-items-center border rounded p-2">
                        <input class="form-check-input" type="checkbox" name="store_ids[]" value="<?= (int)$s['id'] ?>" <?= in_array($s['id'],$selected) ? 'checked' : '' ?>>
                        <span class="small"><?= esc($s['name']) ?></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="btn bm-btn-primary mt-3">Simpan</button>
    </form>
</div>
<?= $this->endSection() ?>
