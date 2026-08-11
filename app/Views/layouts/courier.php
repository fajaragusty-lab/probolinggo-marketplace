<?php
$uri = uri_string();
$courierName = session()->get('user_name') ?? 'Kurir';
$courierInitial = mb_substr($courierName, 0, 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Courier App') ?> - BersolekMart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body class="bm-admin-body">
<div class="bm-admin-shell" id="courierShell">
    <aside class="bm-admin-sidebar" id="courierSidebar">
        <div class="bm-admin-sidebar-head">
            <div>
                <div class="bm-admin-kicker">Courier Field App</div>
                <h5 class="text-white mb-0">BersolekMart</h5>
            </div>
            <button class="btn btn-sm btn-outline-light d-lg-none" type="button" data-bm-sidebar-close>
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="bm-admin-nav-group">
            <div class="bm-admin-nav-label">Field Operations</div>
            <a href="<?= site_url('courier/dashboard') ?>" class="<?= str_starts_with($uri, 'courier/dashboard') ? 'active' : '' ?>">
                <i class="bi bi-phone"></i><span>Field Dashboard</span>
            </a>
            <a href="<?= site_url('courier/dashboard') ?>#available">
                <i class="bi bi-bag-check"></i><span>Available Jobs</span>
            </a>
            <a href="<?= site_url('courier/dashboard') ?>#active">
                <i class="bi bi-truck"></i><span>Active Delivery</span>
            </a>
            <a href="<?= site_url('courier/dashboard') ?>#history">
                <i class="bi bi-clock-history"></i><span>History</span>
            </a>
        </div>
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
                    <div class="bm-admin-kicker">Kurir</div>
                    <h1 class="h5 mb-0"><?= esc($title ?? 'Dashboard') ?></h1>
                </div>
            </div>
            <div class="bm-admin-top-actions">
                <div class="bm-admin-profile">
                    <div class="bm-admin-avatar"><?= esc($courierInitial) ?></div>
                    <div>
                        <div class="fw-semibold small"><?= esc($courierName) ?></div>
                        <div class="small bm-muted">Kurir Aktif</div>
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
