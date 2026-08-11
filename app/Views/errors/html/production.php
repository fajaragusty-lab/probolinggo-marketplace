<?php $statusCode = (int) (http_response_code() ?: 500); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title><?= esc($title ?? 'Terjadi gangguan') ?> — BersolekMart</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
    <div class="bm-error-shell">
        <div class="bm-error-card">
            <div class="bm-error-code"><?= esc((string) $statusCode) ?></div>
            <h1 class="h3 mb-2"><?= esc($title ?? 'Terjadi gangguan pada layanan') ?></h1>
            <p class="bm-muted mb-4">Kami sudah mencatat masalah ini di sistem. Silakan coba beberapa saat lagi atau kembali ke halaman utama marketplace.</p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="<?= site_url('/') ?>" class="btn bm-btn-primary">Kembali ke beranda</a>
                <a href="<?= site_url('orders') ?>" class="btn btn-outline-secondary">Lihat pesanan</a>
            </div>
        </div>
    </div>
</body>
</html>
