<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-4" style="max-width:460px">
    <div class="bm-card p-4">
        <h1 class="h5 mb-3 text-center">Daftar Customer Baru</h1>
        <?php if (session()->getFlashdata('errors')): ?><div class="alert alert-danger small"><?php foreach (session()->getFlashdata('errors') as $e): ?><div><?= esc($e) ?></div><?php endforeach; ?></div><?php endif; ?>
        <form method="post" action="<?= site_url('register') ?>">
            <?= csrf_field() ?>
            <div class="mb-2"><label class="form-label">Nama</label><input type="text" name="name" class="form-control" value="<?= esc(old('name')) ?>" required></div>
            <div class="mb-2"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= esc(old('email')) ?>" required></div>
            <div class="mb-2"><label class="form-label">No. HP</label><input type="text" name="phone" class="form-control" value="<?= esc(old('phone')) ?>"></div>
            <div class="mb-2"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required minlength="8"></div>
            <div class="mb-3"><label class="form-label">Ulangi Password</label><input type="password" name="password_confirmation" class="form-control" required></div>
            <button type="submit" class="btn bm-btn-primary w-100">Daftar</button>
        </form>
        <p class="text-center mt-3 small mb-0">Sudah punya akun? <a href="<?= site_url('login') ?>">Login</a></p>
    </div>
</div>
<?= $this->endSection() ?>
