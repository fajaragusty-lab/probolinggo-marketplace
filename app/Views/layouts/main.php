<?php
$settings = $settings ?? null;
if (!is_array($settings)) {
    $cacheKey = 'marketplace_settings_layout';
    $settings = cache($cacheKey);
    if (!is_array($settings)) {
        $settings = (new \App\Services\MarketplaceSettingsService())->all([
            'app_name' => 'BersolekMart',
            'app_tagline' => 'Marketplace UMKM Probolinggo',
        ]);
        cache()->save($cacheKey, $settings, 300);
    }
}
$brandName = $settings['app_name'] ?? 'BersolekMart';
$brandTagline = $settings['app_tagline'] ?? 'Marketplace UMKM Probolinggo';
$cartCount = (int) ($cartCount ?? (session()->get('user_id') ? model(\App\Models\CartModel::class)->getItemCount((int) session()->get('user_id')) : 0));
$uri = uri_string();
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
        <div class="container py-2">
            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-between">
                <a class="bm-brand" href="<?= site_url('/') ?>"><?= esc($brandName) ?></a>
                <div class="d-none d-md-flex align-items-center gap-2">
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
            <form action="<?= site_url('search') ?>" method="get" class="bm-topsearch mt-2">
                <div class="input-group">
                    <input type="search" name="q" class="form-control" placeholder="Cari produk, toko, atau kategori" value="<?= esc(service('request')->getGet('q') ?? '') ?>">
                    <button class="btn bm-btn-primary" type="submit"><i class="bi bi-search"></i> Cari</button>
                </div>
            </form>
        </div>
    </header>

    <main>
        <?php if (session()->getFlashdata('success')): ?><div class="container mt-3"><div class="alert alert-success mb-0"><?= esc(session()->getFlashdata('success')) ?></div></div><?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?><div class="container mt-3"><div class="alert alert-danger mb-0"><?= esc(session()->getFlashdata('error')) ?></div></div><?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="bm-footer">
        <div class="container">
            <div class="row g-3">
                <div class="col-md-4"><strong><?= esc($brandName) ?></strong><div class="small mt-2">Marketplace UMKM, belanja lokal dengan pengiriman cepat.</div></div>
                <div class="col-6 col-md-2"><div class="small fw-semibold mb-2">Marketplace</div><div class="small">Produk</div><div class="small">Kategori</div><div class="small">Toko</div></div>
                <div class="col-6 col-md-2"><div class="small fw-semibold mb-2">UMKM</div><div class="small">Pusat Seller</div><div class="small">Promosi</div><div class="small">Laporan</div></div>
                <div class="col-6 col-md-2"><div class="small fw-semibold mb-2">Bantuan</div><div class="small">Kontak</div><div class="small">FAQ</div><div class="small">Pengiriman</div></div>
                <div class="col-6 col-md-2"><div class="small fw-semibold mb-2">Legal</div><div class="small">Privasi</div><div class="small">S&K</div></div>
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
