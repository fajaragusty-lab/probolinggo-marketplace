<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3">
    <div class="d-flex justify-content-between mb-3"><h1 class="h5 mb-0">Alamat</h1><a href="<?= site_url('addresses/create') ?>" class="btn btn-bm btn-sm">+ Tambah</a></div>
    <div class="mb-2"><a href="<?= site_url('logout') ?>" class="small text-danger">Logout</a></div>
    <?php foreach ($addresses as $a): ?>
    <div class="card border-0 shadow-sm mb-2"><div class="card-body p-3">
        <strong><?= esc($a['label']) ?></strong> <?= $a['is_default'] ? '<span class="badge bg-success">Default</span>' : '' ?>
        <div class="small"><?= esc($a['recipient_name']) ?> · <?= esc($a['phone']) ?></div>
        <div class="small text-muted"><?= esc($a['address']) ?>, <?= esc($a['district']) ?></div>
        <a href="<?= site_url('addresses/' . $a['id'] . '/edit') ?>" class="btn btn-sm btn-outline-secondary mt-1">Edit</a>
    </div></div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
