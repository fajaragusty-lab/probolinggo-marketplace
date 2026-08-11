<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <div class="bm-card p-3 mb-3">
        <form action="<?= site_url('search') ?>" method="get" class="row g-2 align-items-end">
            <div class="col-md-6"><label class="form-label small bm-muted">Kata kunci</label><input type="search" name="q" class="form-control" value="<?= esc($q ?? '') ?>" placeholder="Cari produk"></div>
            <div class="col-md-4"><label class="form-label small bm-muted">Kategori</label><select name="category" class="form-select"><option value="">Semua kategori</option><?php foreach (($categories ?? []) as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)($categoryId ?? '') === (string)$c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><button class="btn bm-btn-primary w-100">Terapkan</button></div>
        </form>
    </div>
    <?php if (empty($products ?? [])): ?>
        <div class="bm-empty"><h3 class="h6 mb-1">Produk tidak ditemukan</h3><div class="small bm-muted">Coba kata kunci lain atau ubah filter kategori.</div></div>
    <?php else: ?>
        <div class="row g-2 g-md-3"><?php foreach ($products as $product): ?><div class="col-6 col-md-3"><?= view('components/product_card', ['product' => $product]) ?></div><?php endforeach; ?></div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
