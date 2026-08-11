<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Admin') ?> - BersolekMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background: #f4f6fb; }
        .sidebar { min-height: 100vh; background: #1b263b; color: #fff; }
        .sidebar a { color: #cfd9e6; text-decoration: none; display: block; padding: 10px 12px; border-radius: 8px; }
        .sidebar a:hover,.sidebar a.active { background: #273b59; color: #fff; }
        .kpi { border: 0; border-radius: 12px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-lg-2 p-3 sidebar">
            <h5 class="mb-3">BERSOLEKMART</h5>
            <a href="<?= site_url('admin/dashboard') ?>" class="<?= str_contains(uri_string(),'admin/dashboard') || uri_string()==='admin' ? 'active' : '' ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a>
            <a href="<?= site_url('admin/statistics') ?>" class="<?= str_contains(uri_string(),'admin/statistics') ? 'active' : '' ?>"><i class="bi bi-bar-chart me-2"></i>Statistics</a>
            <a href="<?= site_url('admin/reports') ?>" class="<?= str_contains(uri_string(),'admin/reports') ? 'active' : '' ?>"><i class="bi bi-file-earmark-text me-2"></i>Reports</a>
            <a href="<?= site_url('admin/homepage') ?>" class="<?= str_contains(uri_string(),'admin/homepage') ? 'active' : '' ?>"><i class="bi bi-house-gear me-2"></i>Homepage</a>
            <a href="<?= site_url('admin/banners') ?>" class="<?= str_contains(uri_string(),'admin/banners') ? 'active' : '' ?>"><i class="bi bi-images me-2"></i>Banners</a>
            <a href="<?= site_url('admin/featured-products') ?>" class="<?= str_contains(uri_string(),'admin/featured-products') ? 'active' : '' ?>"><i class="bi bi-stars me-2"></i>Featured Products</a>
            <a href="<?= site_url('admin/featured-stores') ?>" class="<?= str_contains(uri_string(),'admin/featured-stores') ? 'active' : '' ?>"><i class="bi bi-shop me-2"></i>Featured Stores</a>
            <a href="<?= site_url('admin/settings') ?>" class="<?= str_contains(uri_string(),'admin/settings') ? 'active' : '' ?>"><i class="bi bi-sliders me-2"></i>Settings</a>
            <a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Logout</a>
        </aside>
        <main class="col-lg-10 p-3 p-lg-4">
            <?php if (session()->getFlashdata('success')): ?><div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div><?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?><div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div><?php endif; ?>
            <?= $this->renderSection('content') ?>
        </main>
    </div>
</div>
</body>
</html>
