<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <div class="d-flex justify-content-between mb-3"><h1 class="h5 mb-0">Alamat Pengiriman</h1><a href="<?= site_url('addresses/create') ?>" class="btn bm-btn-primary btn-sm">+ Tambah</a></div>
    <?php if (empty($addresses)): ?><div class="bm-empty">Belum ada alamat, tambahkan alamat baru untuk checkout.</div><?php endif; ?>
    <?php foreach ($addresses as $a): ?>
        <div class="bm-card p-3 mb-2">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                    <strong><?= esc($a['label']) ?></strong> <?= $a['is_default'] ? '<span class="bm-status is-success">Default</span>' : '' ?>
                    <div class="small"><?= esc($a['recipient_name']) ?> · <?= esc($a['phone']) ?></div>
                    <div class="small bm-muted"><?= esc($a['address']) ?>, <?= esc($a['district']) ?>, <?= esc($a['city']) ?></div>
                </div>
                <a href="<?= site_url('addresses/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary">Edit</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
