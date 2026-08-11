<?php
$uri = uri_string();
$segments = array_values(array_filter(explode('/', $uri)));
$notificationCount = 0;
if (session()->get('user_id')) {
    try {
        $notificationCount = (int) (\Config\Database::connect()->table('notifications')->where('user_id', (int) session()->get('user_id'))->where('is_read', 0)->countAllResults());
    } catch (\Throwable) {
        $notificationCount = 0;
    }
}
$menuGroups = [
    'Overview' => [
        ['path' => 'admin/dashboard', 'label' => 'Dashboard', 'icon' => 'bi-speedometer2'],
        ['path' => 'admin/analytics', 'label' => 'Analytics', 'icon' => 'bi-graph-up-arrow'],
        ['path' => 'admin/reports', 'label' => 'Reports', 'icon' => 'bi-file-earmark-bar-graph'],
    ],
    'Marketplace' => [
        ['path' => 'admin/orders', 'label' => 'Orders', 'icon' => 'bi-receipt'],
        ['path' => 'admin/products', 'label' => 'Products', 'icon' => 'bi-box-seam'],
        ['path' => 'admin/categories', 'label' => 'Categories', 'icon' => 'bi-grid'],
        ['path' => 'admin/umkm', 'label' => 'UMKM', 'icon' => 'bi-shop'],
        ['path' => 'admin/couriers', 'label' => 'Couriers', 'icon' => 'bi-truck'],
        ['path' => 'admin/customers', 'label' => 'Customers', 'icon' => 'bi-people'],
    ],
    'Content' => [
        ['path' => 'admin/homepage', 'label' => 'Homepage CMS', 'icon' => 'bi-window-stack'],
        ['path' => 'admin/banners', 'label' => 'Banners / Promotions', 'icon' => 'bi-images'],
        ['path' => 'admin/featured-products', 'label' => 'Featured Products', 'icon' => 'bi-stars'],
        ['path' => 'admin/featured-stores', 'label' => 'Featured Stores', 'icon' => 'bi-shop-window'],
        ['path' => 'admin/categories', 'label' => 'Categories Display', 'icon' => 'bi-layout-three-columns'],
    ],
    'System' => [
        ['path' => 'admin/settings', 'label' => 'Settings', 'icon' => 'bi-gear'],
        ['path' => 'admin/users', 'label' => 'Users / Admins', 'icon' => 'bi-person-badge'],
        ['path' => 'admin/audit-log', 'label' => 'Audit Log', 'icon' => 'bi-journal-text'],
    ],
];
?>
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
<body class="bm-admin-body">
<div class="bm-admin-shell">
    <aside class="bm-admin-sidebar" id="adminSidebar">
        <div class="bm-admin-sidebar-head">
            <div>
                <div class="bm-admin-kicker">Operations Control Center</div>
                <h5 class="text-white mb-0">BersolekMart Ops</h5>
            </div>
            <button class="btn btn-sm btn-outline-light d-lg-none" type="button" data-bm-sidebar-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <?php foreach ($menuGroups as $group => $items): ?>
            <div class="bm-admin-nav-group">
                <div class="bm-admin-nav-label"><?= esc($group) ?></div>
                <?php foreach ($items as $item): ?>
                    <a href="<?= site_url($item['path']) ?>" class="<?= str_starts_with($uri, $item['path']) ? 'active' : '' ?>">
                        <i class="bi <?= esc($item['icon']) ?>"></i>
                        <span><?= esc($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <div class="bm-admin-sidebar-foot">
            <a href="<?= site_url('logout') ?>"><i class="bi bi-box-arrow-right"></i><span>Logout</span></a>
        </div>
    </aside>
    <div class="bm-admin-backdrop" data-bm-sidebar-close></div>

    <main class="bm-admin-main">
        <header class="bm-admin-top">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bm-sidebar-open>
                    <i class="bi bi-list"></i>
                </button>
                <div>
                    <div class="bm-admin-kicker">Admin Panel</div>
                    <h1 class="h5 mb-1"><?= esc($title ?? 'Dashboard') ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb bm-breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Admin</a></li>
                            <?php foreach ($segments as $index => $segment): ?>
                                <?php if ($segment === 'admin') {
                                    continue;
                                } ?>
                                <li class="breadcrumb-item <?= $index === array_key_last($segments) ? 'active' : '' ?>">
                                    <?= esc(ucwords(str_replace(['-', '_'], ' ', $segment))) ?>
                                </li>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="bm-admin-top-actions">
                <div class="bm-top-meta">
                    <span class="bm-chip"><i class="bi bi-bell"></i> <?= $notificationCount ?> notifikasi</span>
                    <span class="bm-chip"><i class="bi bi-shield-check"></i> Secure session</span>
                </div>
                <div class="bm-admin-profile">
                    <div class="bm-admin-avatar"><?= esc(mb_substr(session()->get('user_name') ?? 'A', 0, 1)) ?></div>
                    <div>
                        <div class="fw-semibold small"><?= esc(session()->get('user_name') ?? 'Admin') ?></div>
                        <div class="small bm-muted"><?= esc(session()->get('user_email') ?? 'ops@bersolekmart.test') ?></div>
                    </div>
                </div>
            </div>
        </header>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success bm-alert"><i class="bi bi-check-circle"></i><span><?= esc(session()->getFlashdata('success')) ?></span></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger bm-alert"><i class="bi bi-exclamation-octagon"></i><span><?= esc(session()->getFlashdata('error')) ?></span></div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
