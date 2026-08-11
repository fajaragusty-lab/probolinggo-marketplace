<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-kpi-grid">
    <div class="bm-kpi"><div class="small bm-muted">Active Banners</div><div class="h5 mb-0"><?= count($banners) ?></div><a href="<?= site_url('admin/banners') ?>" class="small text-primary">Manage</a></div>
    <div class="bm-kpi"><div class="small bm-muted">Featured Products</div><div class="h5 mb-0"><?= count($featuredProducts) ?></div><a href="<?= site_url('admin/featured-products') ?>" class="small text-primary">Manage</a></div>
    <div class="bm-kpi"><div class="small bm-muted">Featured Stores</div><div class="h5 mb-0"><?= count($featuredStores) ?></div><a href="<?= site_url('admin/featured-stores') ?>" class="small text-primary">Manage</a></div>
</div>
<div class="bm-card p-3 mt-3">
    <h2 class="h6">Homepage Preview States</h2>
    <div class="small bm-muted">Perubahan dari modul CMS otomatis tampil di halaman utama customer untuk banner, featured products, dan featured stores.</div>
</div>
<?= $this->endSection() ?>
