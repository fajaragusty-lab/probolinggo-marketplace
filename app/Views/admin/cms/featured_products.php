<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Featured Products</h1>
<form method="post" action="<?= site_url('admin/featured-products') ?>" class="card shadow-sm p-3">
    <?= csrf_field() ?>
    <div class="row g-2">
        <?php foreach ($products as $p): ?>
            <div class="col-md-6">
                <label class="form-check-label d-flex gap-2 align-items-center">
                    <input class="form-check-input" type="checkbox" name="product_ids[]" value="<?= (int)$p['id'] ?>" <?= in_array($p['id'],$selected) ? 'checked' : '' ?>>
                    <span><?= esc($p['name']) ?> <small class="text-muted">(<?= esc($p['store_name']) ?>)</small></span>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="btn btn-primary mt-3">Save Featured Products</button>
</form>
<?= $this->endSection() ?>
