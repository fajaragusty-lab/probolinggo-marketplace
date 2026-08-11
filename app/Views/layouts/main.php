<?php
use App\Services\MarketplaceSettingsService;

$settings = is_array($settings ?? null) ? $settings : (new MarketplaceSettingsService())->all([
    'app_name' => 'BersolekMart',
    'app_tagline' => 'Marketplace UMKM Probolinggo',
]);
$brandName = $settings['app_name'] ?? 'BersolekMart';
$brandTagline = $settings['app_tagline'] ?? 'Marketplace UMKM Probolinggo';
$cartCount = (int) ($cartCount ?? 0);
$uri = uri_string();
$categoryLinks = [];
try {
    $categoryLinks = \Config\Database::connect()->table('categories')->select('name, slug')->where('is_active', 1)->orderBy('sort_order', 'ASC')->limit(8)->get()->getResultArray();
} catch (\Throwable) {
    $categoryLinks = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1d4ed8">
    <title><?= esc($title ?? $brandName) ?> — <?= esc($brandTagline) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="bm-shell">
    <header class="bm-topbar">
        <div class="container py-3">
            <div class="bm-topbar-inner">
                <div class="d-flex flex-wrap align-items-center gap-3 justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <a class="bm-brand" href="<?= site_url('/') ?>"><?= esc($brandName) ?></a>
                        <div class="bm-brand-subtitle d-none d-lg-block"><?= esc($brandTagline) ?></div>
                    </div>

                    <div class="bm-desktop-links">
                        <button id="pwaInstallBtn" class="btn btn-sm btn-light border d-none">Install App</button>
                        <span class="bm-chip"><i class="bi bi-geo-alt"></i> Probolinggo</span>
                        <a class="btn btn-sm btn-light border" href="<?= site_url('orders') ?>">Pesanan</a>
                        <a class="btn btn-sm btn-light border position-relative" href="<?= site_url('cart') ?>">
                            <i class="bi bi-cart3"></i>
                            <?php if ($cartCount > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $cartCount ?></span><?php endif; ?>
                        </a>
                        <?php if (session()->get('user_id')): ?>
                            <a class="btn btn-sm btn-light border" href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right"></i></a>
                        <?php else: ?>
                            <a class="btn btn-sm bm-btn-primary" href="<?= site_url('login') ?>">Masuk</a>
                        <?php endif; ?>
                    </div>
                </div>

                <form action="<?= site_url('search') ?>" method="get" class="bm-topsearch">
                    <div class="input-group">
                        <input type="search" name="q" class="form-control" placeholder="Cari produk, toko, atau kategori" value="<?= esc(service('request')->getGet('q') ?? '') ?>">
                        <button class="btn bm-btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                    </div>
                </form>

                <?php if (!empty($categoryLinks)): ?>
                    <nav class="bm-category-nav d-none d-md-flex">
                        <?php foreach ($categoryLinks as $categoryLink): ?>
                            <a class="bm-category-pill" href="<?= site_url('category/' . $categoryLink['slug']) ?>"><?= esc($categoryLink['name']) ?></a>
                        <?php endforeach; ?>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main>
        <?php if (session()->getFlashdata('success')): ?><div class="container mt-3"><div class="alert alert-success bm-alert mb-0"><i class="bi bi-check-circle"></i><span><?= esc(session()->getFlashdata('success')) ?></span></div></div><?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?><div class="container mt-3"><div class="alert alert-danger bm-alert mb-0"><i class="bi bi-exclamation-octagon"></i><span><?= esc(session()->getFlashdata('error')) ?></span></div></div><?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bm-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <strong><?= esc($brandName) ?></strong>
                    <div class="small mt-2"><?= esc($brandTagline) ?></div>
                    <div class="small mt-2">Marketplace UMKM Kota Probolinggo dengan kurasi produk lokal, pengiriman cepat, dan pengalaman belanja yang aman.</div>
                </div>
                <div class="col-6 col-lg-2"><div class="small fw-semibold mb-2">Marketplace</div><div class="small">Produk</div><div class="small">Kategori</div><div class="small">Toko</div></div>
                <div class="col-6 col-lg-2"><div class="small fw-semibold mb-2">Seller</div><div class="small">Pusat UMKM</div><div class="small">Promosi</div><div class="small">Laporan</div></div>
                <div class="col-6 col-lg-2"><div class="small fw-semibold mb-2">Bantuan</div><div class="small">Kontak</div><div class="small">FAQ</div><div class="small">Pengiriman</div></div>
                <div class="col-6 col-lg-2"><div class="small fw-semibold mb-2">Sosial</div><div class="small"><?= esc($settings['social_instagram'] ?? '@bersolekmart') ?></div><div class="small"><?= esc($settings['social_whatsapp'] ?? 'WhatsApp') ?></div></div>
            </div>
        </div>
    </footer>
</div>

<nav class="bm-mobile-nav d-md-none">
    <a href="<?= site_url('/') ?>" class="<?= $uri === '' ? 'active' : '' ?>"><i class="bi bi-house d-block"></i>Home</a>
    <a href="<?= site_url('search') ?>" class="<?= str_starts_with($uri, 'search') ? 'active' : '' ?>"><i class="bi bi-search d-block"></i>Cari</a>
    <a href="<?= site_url('cart') ?>" class="<?= $uri === 'cart' ? 'active' : '' ?>"><i class="bi bi-cart3 d-block"></i>Cart</a>
    <a href="<?= site_url('orders') ?>" class="<?= str_starts_with($uri, 'orders') ? 'active' : '' ?>"><i class="bi bi-receipt d-block"></i>Order</a>
    <a href="<?= session()->get('user_id') ? site_url('addresses') : site_url('login') ?>"><i class="bi bi-person d-block"></i>Akun</a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
