<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $editing = $editing ?? null; ?>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Total Categories</div><div class="h4 mb-0"><?= (int) ($stats['total'] ?? 0) ?></div></div></div>
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Active Categories</div><div class="h4 mb-0"><?= (int) ($stats['active'] ?? 0) ?></div></div></div>
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Products Covered</div><div class="h4 mb-0"><?= (int) ($stats['products'] ?? 0) ?></div></div></div>
</div>

<div class="bm-layout-split mb-3">
    <div class="bm-card p-3">
        <h2 class="h6 mb-3"><?= $editing ? 'Edit Category' : 'Create Category' ?></h2>
        <form method="post" action="<?= site_url('admin/categories') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
            <div class="bm-form-grid">
                <div class="bm-col-12"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= esc($editing['name'] ?? old('name') ?? '') ?>" required></div>
                <div class="bm-col-6"><label class="form-label">Slug</label><input class="form-control" name="slug" value="<?= esc($editing['slug'] ?? old('slug') ?? '') ?>" placeholder="auto dari nama"></div>
                <div class="bm-col-6"><label class="form-label">Icon / Emoji</label><input class="form-control" name="icon" value="<?= esc($editing['icon'] ?? old('icon') ?? '') ?>" placeholder="🍜"></div>
                <div class="bm-col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3"><?= esc($editing['description'] ?? old('description') ?? '') ?></textarea></div>
                <div class="bm-col-6"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="<?= esc($editing['sort_order'] ?? old('sort_order') ?? 0) ?>"></div>
                <div class="bm-col-6 form-check align-self-end"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="categoryActive" <?= (int) ($editing['is_active'] ?? old('is_active') ?? 1) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="categoryActive">Aktif untuk customer</label></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn bm-btn-primary"><?= $editing ? 'Update Category' : 'Save Category' ?></button>
                <?php if ($editing): ?><a href="<?= site_url('admin/categories') ?>" class="btn btn-outline-secondary">Batal</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="bm-card p-3">
        <h2 class="h6 mb-3">Filter & Search</h2>
        <form method="get" action="<?= site_url('admin/categories') ?>" class="bm-form-grid">
            <div class="bm-col-12"><label class="form-label">Cari kategori</label><input class="form-control" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Nama, slug, deskripsi"></div>
            <div class="bm-col-12"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">Semua</option><option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="bm-col-12 d-flex gap-2"><button class="btn btn-outline-primary">Terapkan</button><a href="<?= site_url('admin/categories') ?>" class="btn btn-outline-secondary">Reset</a></div>
        </form>
    </div>
</div>

<div class="bm-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h2 class="h6 mb-1">Operational Category List</h2><div class="small bm-muted">Kelola kategori yang tampil di katalog dan checkout search.</div></div>
        <div class="small bm-muted"><?= count($categories ?? []) ?> record</div>
    </div>
    <?php if (empty($categories)): ?>
        <div class="bm-empty"><div class="h6 mb-1">Belum ada kategori</div><div class="small bm-muted">Buat kategori pertama untuk membuka struktur katalog marketplace.</div></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="bm-table">
                <thead><tr><th>Kategori</th><th>Slug</th><th>Status</th><th>Urutan</th><th>Produk</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><div class="fw-semibold small"><?= esc($category['name']) ?></div><div class="small bm-muted"><?= esc($category['description'] ?: '-') ?></div></td>
                        <td><div class="small"><?= esc($category['slug']) ?></div><div class="small bm-muted"><?= esc($category['icon'] ?: '-') ?></div></td>
                        <td><span class="bm-status <?= bm_status_class((int) $category['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE') ?>"><?= (int) $category['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE' ?></span></td>
                        <td><?= (int) $category['sort_order'] ?></td>
                        <td><?= (int) $category['product_count'] ?></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/categories?edit=' . (int) $category['id']) ?>">Edit</a>
                                <form method="post" action="<?= site_url('admin/categories/' . (int) $category['id'] . '/toggle') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><?= (int) $category['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
                                <form method="post" action="<?= site_url('admin/categories/' . (int) $category['id'] . '/delete') ?>" onsubmit="return confirm('Hapus kategori ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
