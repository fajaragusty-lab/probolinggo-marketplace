<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-4" style="max-width:460px">
    <div class="bm-card p-4">
        <h1 class="h5 mb-1 text-center">Masuk ke BersolekMart</h1>
        <p class="small bm-muted text-center mb-3">Akses customer, seller, admin, dan courier</p>
        <?php if (session()->getFlashdata('errors')): ?><div class="alert alert-danger small"><?php foreach (session()->getFlashdata('errors') as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="post" action="<?= site_url('login') ?>">
            <?= csrf_field() ?>
            <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required></div>
            <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn bm-btn-primary w-100">Login</button>
        </form>
        <p class="text-center mt-3 small mb-1">Belum punya akun? <a href="<?= site_url('register') ?>">Daftar</a></p>
        <p class="text-center text-muted small mb-0">Demo customer: customer@marketplace.test / password</p>
    </div>
</div>
<?= $this->endSection() ?>
