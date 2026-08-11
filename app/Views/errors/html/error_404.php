<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 — BersolekMart</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
    <div class="bm-error-shell">
        <div class="bm-error-card">
            <div class="bm-error-code">404</div>
            <h1 class="h3 mb-2">Halaman tidak ditemukan</h1>
            <p class="bm-muted mb-4"><?= ENVIRONMENT !== 'production' ? nl2br(esc($message)) : 'Maaf, halaman yang Anda cari tidak tersedia atau sudah dipindahkan.' ?></p>
            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <a href="<?= site_url('/') ?>" class="btn bm-btn-primary">Kembali ke beranda</a>
                <a href="<?= site_url('search') ?>" class="btn btn-outline-secondary">Cari produk</a>
            </div>
        </div>
    </div>
</body>
</html>
