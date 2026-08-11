<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Homepage Management</h1>
<div class="row g-3">
    <div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Active Banners</div><div class="h4 mb-0"><?= count($banners) ?></div><a href="<?= site_url('admin/banners') ?>" class="small">Manage</a></div></div></div>
    <div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Featured Products</div><div class="h4 mb-0"><?= count($featuredProducts) ?></div><a href="<?= site_url('admin/featured-products') ?>" class="small">Manage</a></div></div></div>
    <div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted">Featured Stores</div><div class="h4 mb-0"><?= count($featuredStores) ?></div><a href="<?= site_url('admin/featured-stores') ?>" class="small">Manage</a></div></div></div>
</div>
<?= $this->endSection() ?>
