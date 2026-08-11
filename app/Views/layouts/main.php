<?php
$settings = $settings ?? null;
if (!is_array($settings)) {
    $settings = (new \App\Services\MarketplaceSettingsService())->all([
        'app_name' => 'BersolekMart',
        'app_tagline' => 'Marketplace UMKM Probolinggo',
    ]);
}
$brandName = $settings['app_name'] ?? 'BersolekMart';
$brandTagline = $settings['app_tagline'] ?? 'Marketplace UMKM Probolinggo';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#e85d04">
    <title><?= esc($title ?? $brandName) ?> — <?= esc($brandTagline) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="manifest" href="<?= base_url('manifest.json') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <style>
        :root { --bm-primary: #e85d04; --bm-dark: #1a1a2e; --bm-soft: #fff8f0; }
        body { background: var(--bm-soft); font-family: Inter, system-ui, -apple-system, sans-serif; padding-bottom: 72px; }
        .btn-bm { background: var(--bm-primary); color: #fff; border: none; }
        .btn-bm:hover { background: #d00000; color: #fff; }
        .product-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
        .product-card .price { color: var(--bm-primary); font-weight: 700; }
        .bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; border-top: 1px solid #eee; z-index: 1030; display: flex; justify-content: space-around; padding: 8px 0 env(safe-area-inset-bottom); }
        .bottom-nav a { color: #666; text-decoration: none; font-size: 11px; text-align: center; flex: 1; padding: 4px; }
        .bottom-nav a.active, .bottom-nav a:hover { color: var(--bm-primary); }
        .bottom-nav .icon { font-size: 18px; display: block; margin-bottom: 2px; }
        .hero-bm { background: linear-gradient(135deg, #e85d04, #f48c06); color: #fff; border-radius: 0 0 24px 24px; padding: 28px 16px 24px; }
        .badge-cart { position: absolute; top: -4px; right: 8px; background: #d00000; color: #fff; border-radius: 50%; font-size: 10px; min-width: 16px; height: 16px; line-height: 16px; }
        .alert-fixed { position: sticky; top: 0; z-index: 1040; }
    </style>
</head>
<body>
<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show alert-fixed m-2" role="alert">
    <?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show alert-fixed m-2" role="alert">
    <?= esc(session()->getFlashdata('error')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?= $this->renderSection('content') ?>

<nav class="bottom-nav d-md-none">
    <a href="<?= site_url('/') ?>" class="<?= uri_string() === '' ? 'active' : '' ?>"><span class="icon"><i class="bi bi-house"></i></span>Home</a>
    <a href="<?= site_url('search') ?>" class="<?= str_starts_with(uri_string(), 'search') ? 'active' : '' ?>"><span class="icon"><i class="bi bi-search"></i></span>Cari</a>
    <a href="<?= site_url('categories') ?>" class="<?= str_starts_with(uri_string(), 'categories') || str_starts_with(uri_string(), 'category') ? 'active' : '' ?>"><span class="icon"><i class="bi bi-grid"></i></span>Kategori</a>
    <a href="<?= site_url('cart') ?>" class="position-relative <?= uri_string() === 'cart' ? 'active' : '' ?>">
        <span class="icon"><i class="bi bi-cart3"></i></span>Keranjang
        <?php if (!empty($cartCount ?? 0)): ?><span class="badge-cart"><?= (int)$cartCount ?></span><?php endif; ?>
    </a>
    <a href="<?= session()->get('user_id') ? site_url('addresses') : site_url('login') ?>"><span class="icon"><i class="bi bi-person"></i></span>Akun</a>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
