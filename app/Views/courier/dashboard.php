<?= $this->extend('layouts/courier') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h4 mb-0">Courier Dashboard</h1><a href="<?= site_url('logout') ?>" class="btn btn-outline-secondary btn-sm">Logout</a></div>
<p class="text-muted small">Status: <strong><?= esc($courier['status']) ?></strong> · Completed today: <strong><?= (int)$completedToday ?></strong></p>
<div class="card mb-3"><div class="card-header">Available Jobs</div><div class="card-body">
<?php if(empty($available)): ?><div class="text-muted small">No available shipment.</div><?php endif; ?>
<?php foreach($available as $s): ?>
    <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center">
        <div><div class="fw-semibold"><?= esc($s['shipment_number']) ?></div><div class="small text-muted">Order #<?= (int)$s['order_id'] ?></div></div>
        <form method="post" action="<?= site_url('courier/shipments/' . (int)$s['id'] . '/accept') ?>"><?= csrf_field() ?><button class="btn btn-primary btn-sm">Accept</button></form>
    </div>
<?php endforeach; ?>
</div></div>
<div class="card"><div class="card-header">Active Delivery</div><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Shipment</th><th>Status</th><th>Updated</th></tr></thead><tbody><?php foreach($active as $a): ?><tr><td><?= esc($a['shipment_number']) ?></td><td><?= esc($a['status']) ?></td><td><?= esc($a['updated_at']) ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<?= $this->endSection() ?>
