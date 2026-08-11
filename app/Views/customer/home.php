<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="hero-bm">
    <div class="container">
        <h1 class="h4 mb-1 fw-bold"><?= esc($settings['app_name'] ?? 'BersolekMart') ?></h1>
        <p class="mb-3 opacity-90 small"><?= esc($settings['app_tagline'] ?? 'Temukan produk terbaik dari UMKM Kota Probolinggo.') ?></p>
        <form action="<?= site_url('search') ?>" method="get">
            <div class="input-group">
                <input type="search" name="q" class="form-control form-control-lg border-0" placeholder="Cari produk, toko, atau kategori..." style="border-radius:12px 0 0 12px">
                <button class="btn btn-dark" type="submit" style="border-radius:0 12px 12px 0">Cari</button>
            </div>
        </form>
    </div>
</div>

<div class="container py-3">
    <?php if (!empty($banners)): ?>
        <div id="homeBanner" class="carousel slide mb-3" data-bs-ride="carousel">
            <div class="carousel-inner rounded-4 shadow-sm">
                <?php foreach ($banners as $i => $banner): ?>
                    <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
                        <div class="p-4 text-white" style="min-height:140px;background:linear-gradient(135deg,#f48c06,#e85d04)">
                            <div class="fw-bold"><?= esc($banner['title']) ?></div>
                            <div class="small opacity-75 mb-2"><?= esc($banner['subtitle'] ?? '') ?></div>
                            <?php if (!empty($banner['cta_url']) && !empty($banner['cta_label'])): ?>
                                <a href="<?= esc($banner['cta_url']) ?>" class="btn btn-light btn-sm"><?= esc($banner['cta_label']) ?></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <h2 class="h6 text-muted mb-2">Kategori</h2>
    <div class="d-flex gap-2 overflow-auto pb-2 mb-3" style="scrollbar-width:none">
        <?php foreach ($categories as $cat): ?>
        <a href="<?= site_url('category/' . $cat['slug']) ?>" class="btn btn-outline-secondary btn-sm text-nowrap rounded-pill">
            <?= esc($cat['name']) ?>
        </a>
        <?php endforeach; ?>
    </div>

    <h2 class="h6 mb-2">Produk Unggulan</h2>
    <div class="row g-2 mb-4">
        <?php foreach ($featured as $p): ?>
        <div class="col-6 col-md-3">
            <a href="<?= site_url('product/' . $p['slug']) ?>" class="text-decoration-none text-dark">
                <div class="card product-card h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:120px"><i class="bi bi-image text-secondary"></i></div>
                    <div class="card-body p-2">
                        <div class="small text-muted text-truncate"><?= esc($p['store_name']) ?></div>
                        <div class="fw-semibold small text-truncate"><?= esc($p['name']) ?></div>
                        <div class="price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                        <div class="small text-muted">★ <?= number_format($p['rating_avg'], 1) ?> · Stok <?= $p['stock'] ?></div>
                    </div>
                </div>
            </a>
        </div>
        <?php endforeach; ?>
    </div>

    <h2 class="h6 mb-2">Terbaru</h2>
    <div class="row g-2 mb-3">
        <?php foreach ($latest as $p): ?>
        <div class="col-6 col-md-3">
            <a href="<?= site_url('product/' . $p['slug']) ?>" class="text-decoration-none text-dark">
                <div class="card product-card h-100">
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:120px"><i class="bi bi-image text-secondary"></i></div>
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

    <div class="card border-0 shadow-sm mb-3" style="border-radius:12px">
        <div class="card-body small">
            <strong><?= esc($settings['app_name'] ?? 'BersolekMart') ?></strong><br>
            <?= esc($settings['app_tagline'] ?? 'Marketplace UMKM lokal') ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
