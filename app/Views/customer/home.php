<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <section class="bm-hero p-2 p-md-3 mb-4">
        <?php if (!empty($banners)): ?>
            <div id="homeBanner" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner rounded-4">
                    <?php foreach ($banners as $i => $banner): ?>
                        <?php $img = $banner['image'] ?? null; ?>
                        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>" style="background-image:url('<?= esc(bm_image_url($img, $banner['title'] ?? 'Banner')) ?>')">
                            <div class="overlay">
                                <div>
                                    <h2 class="h5 mb-1"><?= esc($banner['title']) ?></h2>
                                    <p class="small mb-2 opacity-75"><?= esc($banner['subtitle'] ?? '') ?></p>
                                    <?php if (!empty($banner['cta_url']) && !empty($banner['cta_label'])): ?>
                                        <a href="<?= esc($banner['cta_url']) ?>" class="btn btn-light btn-sm"><?= esc($banner['cta_label']) ?></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="p-4">
                <h1 class="h4 mb-2"><?= esc($settings['app_name'] ?? 'BersolekMart') ?></h1>
                <p class="mb-0 opacity-75"><?= esc($settings['app_tagline'] ?? '') ?></p>
            </div>
        <?php endif; ?>
    </section>

    <section class="mb-4">
        <h3 class="bm-section-title">Jelajahi Kategori</h3>
        <div class="bm-category-grid">
            <?php foreach ($categories as $cat): ?>
                <a class="bm-category-card" href="<?= site_url('category/' . $cat['slug']) ?>">
                    <div class="fw-semibold small"><?= esc($cat['name']) ?></div>
                    <div class="small bm-muted"><?= esc($cat['icon'] ?? '•') ?> Produk Lokal</div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-2"><h3 class="bm-section-title mb-0">Produk Unggulan</h3><a href="<?= site_url('search') ?>" class="small text-primary">Lihat semua</a></div>
        <div class="row g-2 g-md-3">
            <?php foreach ($featured as $product): ?><div class="col-6 col-md-3"><?= view('components/product_card', ['product' => $product]) ?></div><?php endforeach; ?>
        </div>
    </section>

    <section class="mb-4">
        <h3 class="bm-section-title">Toko Pilihan UMKM</h3>
        <div class="row g-2 g-md-3">
            <?php foreach ($stores as $store): ?><div class="col-12 col-md-6 col-lg-4"><?= view('components/store_card', ['store' => $store]) ?></div><?php endforeach; ?>
        </div>
    </section>

    <?php foreach (['Sedang Trending' => $trending ?? [], 'Terbaru' => $latest ?? [], 'Terpopuler' => $popular ?? [], 'Rekomendasi Untukmu' => $recommended ?? []] as $label => $items): ?>
        <section class="mb-4">
            <h3 class="bm-section-title"><?= esc($label) ?></h3>
            <?php if (empty($items)): ?>
                <div class="bm-empty small bm-muted">Belum ada data untuk section ini.</div>
            <?php else: ?>
                <div class="row g-2 g-md-3"><?php foreach ($items as $product): ?><div class="col-6 col-md-3"><?= view('components/product_card', ['product' => $product]) ?></div><?php endforeach; ?></div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
