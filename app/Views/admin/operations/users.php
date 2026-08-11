<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $editing = $editing ?? null; ?>
<div class="bm-layout-split mb-3">
    <div class="bm-card p-3">
        <h2 class="h6 mb-3"><?= $editing ? 'Edit User' : 'Create User' ?></h2>
        <form method="post" action="<?= site_url('admin/users') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
            <div class="bm-form-grid">
                <div class="bm-col-12"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= esc($editing['name'] ?? old('name') ?? '') ?>" required></div>
                <div class="bm-col-12"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= esc($editing['email'] ?? old('email') ?? '') ?>" required></div>
                <div class="bm-col-6"><label class="form-label">Telepon</label><input class="form-control" name="phone" value="<?= esc($editing['phone'] ?? old('phone') ?? '') ?>"></div>
                <div class="bm-col-6"><label class="form-label">Role</label>
                    <select class="form-select" name="role_slug" required>
                        <?php foreach (($roles ?? []) as $role): ?>
                            <option value="<?= esc($role['slug']) ?>" <?= (($editing['role_slug'] ?? old('role_slug') ?? '') === $role['slug']) ? 'selected' : '' ?>><?= esc($role['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="bm-col-12"><label class="form-label"><?= $editing ? 'Password Baru (opsional)' : 'Password' ?></label><input type="password" class="form-control" name="password" <?= $editing ? '' : 'required' ?>></div>
                <div class="bm-col-12 form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="userActive" <?= (int) ($editing['is_active'] ?? old('is_active') ?? 1) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="userActive">Akun aktif</label></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn bm-btn-primary"><?= $editing ? 'Update User' : 'Save User' ?></button>
                <?php if ($editing): ?><a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">Batal</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="bm-card p-3">
        <h2 class="h6 mb-3">Filter & Search</h2>
        <form method="get" action="<?= site_url('admin/users') ?>" class="bm-form-grid">
            <div class="bm-col-12"><label class="form-label">Cari user</label><input class="form-control" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Nama, email, telepon"></div>
            <div class="bm-col-12"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">Semua</option><option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="bm-col-12 d-flex gap-2"><button class="btn btn-outline-primary">Terapkan</button><a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">Reset</a></div>
        </form>
    </div>
</div>

<div class="bm-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h2 class="h6 mb-1">Users / Admins</h2><div class="small bm-muted">CRUD akun lintas peran untuk operasional marketplace.</div></div>
        <div class="small bm-muted"><?= count($users ?? []) ?> record</div>
    </div>
    <?php if (empty($users)): ?>
        <div class="bm-empty"><div class="h6 mb-1">Belum ada user</div><div class="small bm-muted">Tambahkan user pertama melalui form di samping.</div></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="bm-table">
                <thead><tr><th>User</th><th>Kontak</th><th>Status</th><th>Peran</th><th>Last Login</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><div class="fw-semibold small"><?= esc($user['name']) ?></div><div class="small bm-muted">Registered <?= esc($user['created_at'] ?: '-') ?></div></td>
                        <td><div class="small"><?= esc($user['email']) ?></div><div class="small bm-muted"><?= esc($user['phone'] ?: '-') ?></div></td>
                        <td><span class="bm-status <?= bm_status_class((int) $user['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE') ?>"><?= (int) $user['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE' ?></span></td>
                        <td><div class="small"><?= esc($user['roles'] ?: '-') ?></div></td>
                        <td><div class="small"><?= esc($user['last_login_at'] ?: '-') ?></div></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/users?edit=' . (int) $user['id']) ?>">Edit</a>
                                <form method="post" action="<?= site_url('admin/users/' . (int) $user['id'] . '/toggle') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><?= (int) $user['is_active'] === 1 ? 'Suspend' : 'Activate' ?></button></form>
                                <form method="post" action="<?= site_url('admin/users/' . (int) $user['id'] . '/delete') ?>" onsubmit="return confirm('Hapus user ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
