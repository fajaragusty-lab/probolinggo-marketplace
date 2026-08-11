<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Featured Stores</h1>
<form method="post" action="<?= site_url('admin/featured-stores') ?>" class="card shadow-sm p-3">
    <?= csrf_field() ?>
    <div class="row g-2">
        <?php foreach ($stores as $s): ?>
            <div class="col-md-6">
                <label class="form-check-label d-flex gap-2 align-items-center">
                    <input class="form-check-input" type="checkbox" name="store_ids[]" value="<?= (int)$s['id'] ?>" <?= in_array($s['id'],$selected) ? 'checked' : '' ?>>
                    <span><?= esc($s['name']) ?></span>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
    <button class="btn btn-primary mt-3">Save Featured Stores</button>
</form>
<?= $this->endSection() ?>
