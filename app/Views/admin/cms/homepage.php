<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-kpi-grid mb-3">
    <div class="bm-kpi"><div class="small bm-muted">Active Banners</div><div class="h5 mb-0"><?= count($banners) ?></div><a href="<?= site_url('admin/banners') ?>" class="small text-primary text-decoration-none">Manage banners</a></div>
    <div class="bm-kpi"><div class="small bm-muted">Featured Products</div><div class="h5 mb-0"><?= count($featuredProducts) ?></div><a href="<?= site_url('admin/featured-products') ?>" class="small text-primary text-decoration-none">Manage products</a></div>
    <div class="bm-kpi"><div class="small bm-muted">Featured Stores</div><div class="h5 mb-0"><?= count($featuredStores) ?></div><a href="<?= site_url('admin/featured-stores') ?>" class="small text-primary text-decoration-none">Manage stores</a></div>
</div>

<div class="bm-layout-split">
    <div class="bm-card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 class="h6 mb-1">Homepage merchandising</h2>
                <div class="small bm-muted">Atur urutan section, label tampilan, dan visibilitas homepage customer.</div>
            </div>
        </div>
        <form method="post" action="<?= site_url('admin/homepage') ?>">
            <?= csrf_field() ?>
            <div class="table-responsive">
                <table class="bm-table">
                    <thead><tr><th>Section</th><th>Label</th><th>Urutan</th><th>Tampil</th></tr></thead>
                    <tbody>
                    <?php foreach ($sections as $section): ?>
                        <tr>
                            <td>
                                <strong><?= esc(ucwords(str_replace('_', ' ', $section['key']))) ?></strong>
                                <input type="hidden" name="section_key[]" value="<?= esc($section['key']) ?>">
                            </td>
                            <td><input class="form-control" name="section_label[]" value="<?= esc($section['label']) ?>"></td>
                            <td style="width:120px"><input type="number" class="form-control" name="section_sort[]" value="<?= (int) $section['sort_order'] ?>" min="1"></td>
                            <td style="width:90px"><input class="form-check-input" type="checkbox" name="section_enabled[]" value="<?= esc($section['key']) ?>" <?= !empty($section['enabled']) ? 'checked' : '' ?>></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <button class="btn bm-btn-primary mt-3">Simpan konfigurasi homepage</button>
        </form>
    </div>

    <div class="bm-preview-stack">
        <div class="bm-card p-3">
            <h2 class="h6 mb-2">Homepage Preview States</h2>
            <div class="small bm-muted mb-3">Perubahan dari modul CMS otomatis tampil di halaman utama customer untuk banner, featured products, featured stores, dan section ordering.</div>
            <div class="d-grid gap-2">
                <a href="<?= site_url('admin/banners') ?>" class="btn btn-outline-primary">Kelola hero banners</a>
                <a href="<?= site_url('admin/featured-products') ?>" class="btn btn-outline-primary">Kelola featured products</a>
                <a href="<?= site_url('admin/featured-stores') ?>" class="btn btn-outline-primary">Kelola featured stores</a>
            </div>
        </div>
        <div class="bm-card p-3">
            <h2 class="h6 mb-2">Konten aktif saat ini</h2>
            <div class="small bm-muted">Section aktif: <?= count(array_filter($sections, static fn (array $section) => !empty($section['enabled']))) ?></div>
            <div class="small bm-muted">Banner publish: <?= count($banners) ?></div>
            <div class="small bm-muted">Produk unggulan: <?= count($featuredProducts) ?></div>
            <div class="small bm-muted">Toko unggulan: <?= count($featuredStores) ?></div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
