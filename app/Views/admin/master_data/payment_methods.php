<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $editing = $editing ?? null; ?>
<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Total Methods</div><div class="h4 mb-0"><?= (int) ($stats['total'] ?? 0) ?></div></div></div>
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Active Methods</div><div class="h4 mb-0"><?= (int) ($stats['active'] ?? 0) ?></div></div></div>
    <div class="col-md-4"><div class="bm-card p-3"><div class="small bm-muted">Payments Recorded</div><div class="h4 mb-0"><?= (int) ($stats['payments'] ?? 0) ?></div></div></div>
</div>

<div class="bm-layout-split mb-3">
    <div class="bm-card p-3">
        <h2 class="h6 mb-3"><?= $editing ? 'Edit Payment Method' : 'Create Payment Method' ?></h2>
        <form method="post" action="<?= site_url('admin/payment-methods') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) ($editing['id'] ?? 0) ?>">
            <div class="bm-form-grid">
                <div class="bm-col-6"><label class="form-label">Provider</label><input class="form-control" name="provider" value="<?= esc($editing['provider'] ?? old('provider') ?? '') ?>" required placeholder="midtrans"></div>
                <div class="bm-col-6"><label class="form-label">Method Code</label><input class="form-control" name="method_code" value="<?= esc($editing['method_code'] ?? old('method_code') ?? '') ?>" required placeholder="qris"></div>
                <div class="bm-col-12"><label class="form-label">Method Name</label><input class="form-control" name="method_name" value="<?= esc($editing['method_name'] ?? old('method_name') ?? '') ?>" required placeholder="QRIS"></div>
                <div class="bm-col-12"><label class="form-label">Config JSON</label><textarea class="form-control font-monospace" name="config_json" rows="6" placeholder='{"instruction":"Bayar lewat QRIS"}'><?= esc($editing['config_json'] ?? old('config_json') ?? '') ?></textarea></div>
                <div class="bm-col-12 form-check"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="paymentActive" <?= (int) ($editing['is_active'] ?? old('is_active') ?? 1) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="paymentActive">Aktif dipakai checkout</label></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn bm-btn-primary"><?= $editing ? 'Update Method' : 'Save Method' ?></button>
                <?php if ($editing): ?><a href="<?= site_url('admin/payment-methods') ?>" class="btn btn-outline-secondary">Batal</a><?php endif; ?>
            </div>
        </form>
    </div>

    <div class="bm-card p-3">
        <h2 class="h6 mb-3">Filter & Search</h2>
        <form method="get" action="<?= site_url('admin/payment-methods') ?>" class="bm-form-grid">
            <div class="bm-col-12"><label class="form-label">Cari metode</label><input class="form-control" name="q" value="<?= esc($filters['q'] ?? '') ?>" placeholder="Provider, code, nama"></div>
            <div class="bm-col-12"><label class="form-label">Status</label><select class="form-select" name="status"><option value="">Semua</option><option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="bm-col-12 d-flex gap-2"><button class="btn btn-outline-primary">Terapkan</button><a href="<?= site_url('admin/payment-methods') ?>" class="btn btn-outline-secondary">Reset</a></div>
        </form>
    </div>
</div>

<div class="bm-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h2 class="h6 mb-1">Checkout Payment Catalog</h2><div class="small bm-muted">Kelola payment method aktif yang dipakai customer saat checkout.</div></div>
        <div class="small bm-muted"><?= count($methods ?? []) ?> record</div>
    </div>
    <?php if (empty($methods)): ?>
        <div class="bm-empty"><div class="h6 mb-1">Belum ada metode pembayaran</div><div class="small bm-muted">Tambahkan provider dan method code untuk mengaktifkan checkout selain COD.</div></div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="bm-table">
                <thead><tr><th>Method</th><th>Provider</th><th>Status</th><th>Orders</th><th>Config</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($methods as $method): ?>
                    <tr>
                        <td><div class="fw-semibold small"><?= esc($method['method_name']) ?></div><div class="small bm-muted"><?= esc($method['method_code']) ?></div></td>
                        <td><div class="small"><?= esc($method['provider']) ?></div></td>
                        <td><span class="bm-status <?= bm_status_class((int) $method['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE') ?>"><?= (int) $method['is_active'] === 1 ? 'ACTIVE' : 'INACTIVE' ?></span></td>
                        <td><?= (int) $method['total_orders'] ?></td>
                        <td><div class="small bm-muted" style="max-width:280px; white-space:pre-wrap;"><?= esc($method['config_json'] ?: '-') ?></div></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/payment-methods?edit=' . (int) $method['id']) ?>">Edit</a>
                                <form method="post" action="<?= site_url('admin/payment-methods/' . (int) $method['id'] . '/toggle') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><?= (int) $method['is_active'] === 1 ? 'Nonaktifkan' : 'Aktifkan' ?></button></form>
                                <form method="post" action="<?= site_url('admin/payment-methods/' . (int) $method['id'] . '/delete') ?>" onsubmit="return confirm('Hapus metode pembayaran ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Delete</button></form>
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
