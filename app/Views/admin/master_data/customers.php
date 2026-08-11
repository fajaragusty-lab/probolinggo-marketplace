<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $editing = $editing ?? null; ?>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Total Customers</div><div class="h4 mb-0"><?= (int) ($stats['total'] ?? 0) ?></div></div></div>
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Active Customers</div><div class="h4 mb-0"><?= (int) ($stats['active'] ?? 0) ?></div></div></div>
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Orders Recorded</div><div class="h4 mb-0"><?= (int) ($stats['orders'] ?? 0) ?></div></div></div>
</div>

<div class="bm-layout-split mb-3">
    <div class="bm-card p-3">
        <h2 class="h6 mb-3"><?= $editing ? 'Edit Customer' : 'Create Customer' ?></h2>
        <form method="post" action="<?= site_url('admin/customers') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
            <div class="bm-form-grid">
                <div class="bm-col-12"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= esc($editing['name'] ?? old('name') ?? '') ?>" required></div>
                <div class="bm-col-12"><label class="form-label">Email</label><input type="email" class="form-control" name="email" value="<?= esc($editing['email'] ?? old('email') ?? '') ?>" required></div>
                <div class="bm-col-6"><label class="form-label">Telepon</label><input class="form-control" name="phone" value="<?= esc($editing['phone'] ?? old('phone') ?? '') ?>"></div>
                <div class="bm-col-6"><label class="form-label"><?= $editing ? 'Password Baru (opsional)' : 'Password' ?></label><input type="password" class="form-control" name="password" <?= $editing ? '' : 'required' ?>></div>
                <div class="bm-col-12 form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="customerActive" <?= (int) ($editing['is_active'] ?? old('is_active') ?? 1) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="customerActive">Akun aktif</label></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn bm-btn-primary"><?= $editing ? 'Update Customer' : 'Save Customer' ?></button>
                <?php if ($editing): ?><a href="<?= site_url('admin/customers') ?>" class="btn btn-outline-secondary">Batal</a><?php endif; ?>
            </div>
        </form>

        <?php if ($editing): ?>
            <hr>
            <div class="small fw-semibold mb-2">Alamat customer</div>
            <?php if (empty($addresses)): ?>
                <div class="small bm-muted">Belum ada alamat tersimpan.</div>
            <?php else: ?>
                <?php foreach ($addresses as $address): ?>
                    <div class="border rounded p-2 mb-2">
                        <div class="small fw-semibold"><?= esc($address['label']) ?> <?= (int) $address['is_default'] === 1 ? '<span class="bm-status is-success">Default</span>' : '' ?></div>
                        <div class="small"><?= esc($address['recipient_name']) ?> · <?= esc($address['phone']) ?></div>
                        <div class="small bm-muted"><?= esc($address['address']) ?>, <?= esc($address['district']) ?>, <?= esc($address['city']) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="bm-card p-3">
        <h2 class="h6 mb-3">Filter & Search</h2>
        <form method="get" action="<?= site_url('admin/customers') ?>" class="bm-form-grid">
            <div class="bm-col-12"><label class="form-label">Cari customer</label><input class="form-control" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Nama, email, telepon"></div>
            <div class="bm-col-12"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">Semua</option><option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="bm-col-12 d-flex gap-2"><button class="btn btn-outline-primary">Terapkan</button><a href="<?= site_url('admin/customers') ?>" class="btn btn-outline-secondary">Reset</a></div>
        </form>
    </div>
</div>

<div class="bm-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h2 class="h6 mb-1">Operational Customer List</h2><div class="small bm-muted">Admin bisa menambah, mengaktifkan, memperbarui, dan menonaktifkan akun pelanggan.</div></div>
        <div class="small bm-muted"><?= count($customers ?? []) ?> record</div>
    </div>
    <?php if (empty($customers)): ?>
        <div class="bm-empty"><div class="h6 mb-1">Belum ada customer</div><div class="small bm-muted">Tambahkan customer baru atau tunggu registrasi dari publik.</div></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="bm-table">
                <thead><tr><th>Customer</th><th>Kontak</th><th>Status</th><th>Alamat</th><th>Orders</th><th>Last Login</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><div class="fw-semibold small"><?= esc($customer['name']) ?></div><div class="small bm-muted">Registered <?= esc($customer['created_at'] ?: '-') ?></div></td>
                        <td><div class="small"><?= esc($customer['email']) ?></div><div class="small bm-muted"><?= esc($customer['phone'] ?: '-') ?></div></td>
                        <td><span class="bm-status <?= bm_status_class((int) $customer['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE') ?>"><?= (int) $customer['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE' ?></span></td>
                        <td><?= (int) $customer['total_addresses'] ?></td>
                        <td><?= (int) $customer['total_orders'] ?></td>
                        <td><div class="small"><?= esc($customer['last_login_at'] ?: '-') ?></div></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/customers?edit=' . (int) $customer['id']) ?>">Edit</a>
                                <form method="post" action="<?= site_url('admin/customers/' . (int) $customer['id'] . '/toggle') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><?= (int) $customer['is_active'] === 1 ? 'Suspend' : 'Activate' ?></button></form>
                                <form method="post" action="<?= site_url('admin/customers/' . (int) $customer['id'] . '/delete') ?>" onsubmit="return confirm('Hapus customer ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Delete</button></form>
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
