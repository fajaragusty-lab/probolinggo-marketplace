<?php $uri = uri_string(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'UMKM') ?> - BersolekMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="bm-admin">
    <aside class="bm-admin-sidebar">
        <h5 class="text-white mb-3">Seller Center</h5>
        <a href="<?= site_url('umkm/dashboard') ?>" class="<?= str_starts_with($uri,'umkm/dashboard') ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
        <a href="<?= site_url('umkm/products') ?>" class="<?= str_starts_with($uri,'umkm/products') ? 'active' : '' ?>"><i class="bi bi-box-seam"></i> Products</a>
        <a href="<?= site_url('umkm/orders') ?>" class="<?= str_starts_with($uri,'umkm/orders') ? 'active' : '' ?>"><i class="bi bi-receipt"></i> Orders</a>
        <a href="<?= site_url('umkm/reports') ?>" class="<?= str_starts_with($uri,'umkm/reports') ? 'active' : '' ?>"><i class="bi bi-graph-up"></i> Reports</a>
        <a href="<?= site_url('umkm/store') ?>" class="<?= str_starts_with($uri,'umkm/store') ? 'active' : '' ?>"><i class="bi bi-shop"></i> Store</a>
        <a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </aside>
    <main class="bm-admin-main">
        <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
        <?= $this->renderSection('content') ?>
    </main>
</div>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
</body>
</html>
