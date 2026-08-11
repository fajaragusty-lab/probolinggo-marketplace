<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3">
    <form method="post" action="<?= site_url('admin/featured-products') ?>">
        <?= csrf_field() ?>
        <h2 class="h6 mb-3">Kelola Produk Unggulan Homepage</h2>
        <div class="row g-2">
            <?php foreach ($products as $p): ?>
                <div class="col-md-6">
                    <label class="d-flex gap-2 align-items-center border rounded p-2">
                        <input class="form-check-input" type="checkbox" name="product_ids[]" value="<?= (int)$p['id'] ?>" <?= in_array($p['id'],$selected) ? 'checked' : '' ?>>
                        <span class="small"><strong><?= esc($p['name']) ?></strong><br><span class="bm-muted"><?= esc($p['store_name']) ?></span></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <button class="btn bm-btn-primary mt-3">Simpan</button>
    </form>
</div>
<?= $this->endSection() ?>
