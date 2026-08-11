<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-4" style="max-width:420px">
    <h1 class="h4 text-center mb-1">Masuk BersolekMart</h1>
    <p class="text-center text-muted small mb-4">Marketplace UMKM Kota Probolinggo</p>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger small">
            <?php foreach (session()->getFlashdata('errors') as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="<?= site_url('login') ?>">
        <?= csrf_field() ?>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control form-control-lg" value="<?= esc(old('email')) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-lg" required>
        </div>
        <button type="submit" class="btn btn-bm w-100 btn-lg">Login</button>
    </form>
    <p class="text-center mt-3 small">Belum punya akun? <a href="<?= site_url('register') ?>">Daftar</a></p>
    <p class="text-center text-muted small mt-2">Demo: customer@marketplace.test / password</p>
</div>
<?= $this->endSection() ?>
