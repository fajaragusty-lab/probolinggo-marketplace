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
                                <div class="d-flex flex-column gap-2">
                                    <div class="bm-chip w-auto align-self-start"><i class="bi bi-megaphone"></i> Promo BersolekMart</div>
                                    <div>
                                        <h2 class="display-6 fw-bold mb-2"><?= esc($banner['title']) ?></h2>
                                        <p class="mb-0 opacity-75"><?= esc($banner['subtitle'] ?? '') ?></p>
                                    </div>
                                    <?php if (!empty($banner['cta_url']) && !empty($banner['cta_label'])): ?>
                                        <div><a href="<?= esc($banner['cta_url']) ?>" class="btn btn-light"><?= esc($banner['cta_label']) ?></a></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="p-4 p-md-5">
                <div class="bm-chip mb-3"><i class="bi bi-shop"></i> Marketplace UMKM Probolinggo</div>
                <h1 class="display-6 fw-bold mb-2"><?= esc($settings['app_name'] ?? 'BersolekMart') ?></h1>
                <p class="mb-0 opacity-75"><?= esc($settings['app_tagline'] ?? '') ?></p>
            </div>
        <?php endif; ?>
    </section>

    <section class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="bm-section-title mb-0">Jelajahi Kategori</h3>
            <a href="<?= site_url('categories') ?>" class="small text-primary text-decoration-none">Lihat kategori</a>
        </div>
        <?php if (empty($categories)): ?>
            <div class="bm-empty">
                <h2 class="h6 mb-1">Kategori belum tersedia</h2>
                <p class="small bm-muted mb-0">Admin dapat mengaktifkan kategori produk dari panel backend.</p>
            </div>
        <?php else: ?>
            <div class="bm-category-grid">
                <?php foreach ($categories as $cat): ?>
                    <a class="bm-category-card" href="<?= site_url('category/' . $cat['slug']) ?>">
                        <div class="fw-semibold"><?= esc($cat['name']) ?></div>
                        <div class="small bm-muted"><?= esc($cat['icon'] ?? '•') ?> Produk Lokal</div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <?php foreach (($homepageSections ?? []) as $section): ?>
        <?php if (empty($section['enabled'])) {
            continue;
        }
        if (empty($section['items'])) {
            continue;
        } ?>
        <section class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="bm-section-title mb-0"><?= esc($section['label']) ?></h3>
                <a href="<?= site_url('search') ?>" class="small text-primary text-decoration-none">Lihat semua</a>
            </div>

            <?php if (($section['type'] ?? 'products') === 'stores'): ?>
                <div class="row g-3">
                    <?php foreach ($section['items'] as $store): ?>
                        <div class="col-12 col-md-6 col-lg-4"><?= view('components/store_card', ['store' => $store]) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="row g-2 g-md-3">
                    <?php foreach ($section['items'] as $product): ?>
                        <div class="col-6 col-md-4 col-xl-3"><?= view('components/product_card', ['product' => $product]) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
