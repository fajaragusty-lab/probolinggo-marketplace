<?php $uri = uri_string(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Admin') ?> - BersolekMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="bm-admin">
    <aside class="bm-admin-sidebar">
        <h5 class="text-white mb-3">BersolekMart Ops</h5>
        <?php $menus = [
            'admin/dashboard' => ['Dashboard', 'bi-speedometer2'],
            'admin/statistics' => ['Analytics', 'bi-graph-up-arrow'],
            'admin/reports' => ['Reports', 'bi-file-earmark-bar-graph'],
            'admin/homepage' => ['Homepage CMS', 'bi-window-stack'],
            'admin/banners' => ['Promotions', 'bi-images'],
            'admin/featured-products' => ['Products', 'bi-box-seam'],
            'admin/featured-stores' => ['UMKM', 'bi-shop'],
            'admin/settings' => ['Settings', 'bi-gear'],
        ]; ?>
        <?php foreach ($menus as $path => [$label, $icon]): ?>
            <a href="<?= site_url($path) ?>" class="<?= str_starts_with($uri, $path) ? 'active' : '' ?>"><i class="bi <?= $icon ?>"></i> <?= esc($label) ?></a>
        <?php endforeach; ?>
        <a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </aside>
    <main class="bm-admin-main">
        <div class="bm-admin-top">
            <div>
                <h1 class="h5 mb-0"><?= esc($title ?? 'Dashboard') ?></h1>
                <div class="small bm-muted">Admin / <?= esc(str_replace('/', ' / ', $uri ?: 'dashboard')) ?></div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="bm-chip"><i class="bi bi-bell"></i> Notifikasi</span>
                <span class="bm-chip"><i class="bi bi-person-circle"></i> <?= esc(session()->get('user_name') ?? 'Admin') ?></span>
            </div>
        </div>
        <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
