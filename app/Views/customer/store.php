<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3">
    <form action="<?= site_url('search') ?>" method="get" class="mb-3">
        <div class="input-group">
            <input type="search" name="q" value="<?= esc($q ?? '') ?>" class="form-control" placeholder="Cari produk...">
            <button class="btn btn-bm">Cari</button>
        </div>
    </form>
    <div class="row g-2">
        <?php foreach ($products ?? [] as $p): ?>
        <div class="col-6">
            <a href="<?= site_url('product/' . $p['slug']) ?>" class="text-decoration-none text-dark">
                <div class="card product-card h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:100px;font-size:32px">📦</div>
                    <div class="card-body p-2">
                        <div class="small text-muted text-truncate"><?= esc($p['store_name']) ?></div>
                        <div class="fw-semibold small text-truncate"><?= esc($p['name']) ?></div>
                        <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php if (empty($products)): ?><p class="text-muted text-center py-4">Tidak ada hasil.</p><?php endif; ?>
</div>
<?= $this->endSection() ?>
