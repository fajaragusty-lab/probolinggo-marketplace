<?= $this->extend('layouts/courier') ?>
<?= $this->section('content') ?>
<h1 class="h5 mb-1">Courier Field Dashboard</h1>
<div class="small bm-muted mb-3">Status saat ini: <span class="bm-status <?= bm_status_class($courier['status']) ?>"><?= esc($courier['status']) ?></span></div>

<div class="bm-kpi-grid mb-3">
    <div class="bm-kpi"><div class="small bm-muted">Delivered Today</div><div class="h5 mb-0"><?= (int)$completedToday ?></div></div>
    <div class="bm-kpi"><div class="small bm-muted">Earnings Today</div><div class="h5 mb-0"><?= bm_currency((int)$earningsToday) ?></div></div>
    <div class="bm-kpi"><div class="small bm-muted">Total Deliveries</div><div class="h5 mb-0"><?= (int)$courier['total_deliveries'] ?></div></div>
    <div class="bm-kpi"><div class="small bm-muted">Total Earnings</div><div class="h5 mb-0"><?= bm_currency((int)$courier['total_earnings']) ?></div></div>
</div>

<div class="bm-card p-3 mb-3">
    <h2 class="h6 mb-2">Toggle Online / Offline</h2>
    <form method="post" action="<?= site_url('courier/status') ?>" class="d-flex gap-2 flex-wrap"><?= csrf_field() ?>
        <?php foreach (['OFFLINE','ONLINE','AVAILABLE'] as $status): ?>
            <button name="status" value="<?= $status ?>" class="btn <?= $courier['status'] === $status ? 'bm-btn-primary' : 'btn-outline-secondary' ?> btn-sm"><?= $status ?></button>
        <?php endforeach; ?>
    </form>
</div>

<div id="available" class="bm-card p-3 mb-3">
    <h2 class="h6">Available Jobs</h2>
    <?php if(empty($available)): ?><div class="bm-empty py-3">Tidak ada shipment tersedia saat ini.</div><?php endif; ?>
    <?php foreach($available as $s): ?>
        <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center">
            <div><div class="fw-semibold small"><?= esc($s['shipment_number']) ?></div><div class="small bm-muted">Order #<?= (int)$s['order_id'] ?> · <?= bm_currency((int)$s['delivery_fee']) ?></div></div>
            <form method="post" action="<?= site_url('courier/shipments/' . (int)$s['id'] . '/accept') ?>"><?= csrf_field() ?><button class="btn btn-sm bm-btn-primary">Accept</button></form>
        </div>
    <?php endforeach; ?>
</div>

<div id="active" class="bm-card p-3 mb-3">
    <h2 class="h6">Active Delivery</h2>
    <?php if(empty($active)): ?><div class="bm-empty py-3">Tidak ada delivery aktif.</div><?php endif; ?>
    <?php foreach($active as $a): ?>
        <div class="border rounded p-2 mb-2" data-shipment-tracker data-track-url="<?= esc(site_url('courier/shipments/' . (int) $a['id'] . '/track')) ?>">
            <div class="d-flex justify-content-between"><strong><?= esc($a['shipment_number']) ?></strong><span class="bm-status <?= bm_status_class($a['status']) ?>"><?= esc($a['status']) ?></span></div>
            <div class="small bm-muted mb-2">Pickup: <?= esc($a['pickup_address'] ?? '-') ?><br>Drop: <?= esc($a['delivery_address'] ?? '-') ?></div>
            <div class="d-flex gap-2 flex-wrap">
                <?php if ($a['status'] === 'ACCEPTED'): ?>
                    <form method="post" action="<?= site_url('courier/shipments/' . (int)$a['id'] . '/arrive-pickup') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-primary">Arrive Pickup</button></form>
                <?php endif; ?>
                <?php if ($a['status'] === 'ARRIVED_PICKUP'): ?>
                    <form method="post" action="<?= site_url('courier/shipments/' . (int)$a['id'] . '/pickup') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-primary">Pickup</button></form>
                <?php endif; ?>
                <?php if ($a['status'] === 'PICKED_UP'): ?>
                    <form method="post" action="<?= site_url('courier/shipments/' . (int)$a['id'] . '/delivery') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-primary">Start Delivery</button></form>
                <?php endif; ?>
                <?php if ($a['status'] === 'ON_DELIVERY'): ?>
                    <form method="post" action="<?= site_url('courier/shipments/' . (int)$a['id'] . '/arrive-destination') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-primary">Arrive Destination</button></form>
                <?php endif; ?>
            </div>
            <?php if ($a['status'] === 'ARRIVED_DESTINATION'): ?>
                <form method="post" action="<?= site_url('courier/shipments/' . (int)$a['id'] . '/verify-otp') ?>" class="mt-2 row g-2">
                    <?= csrf_field() ?>
                    <div class="col-md-9"><input class="form-control form-control-sm" name="otp_code" placeholder="OTP penerima" required></div>
                    <div class="col-md-3"><button class="btn btn-sm bm-btn-primary w-100">Verify OTP</button></div>
                </form>
            <?php endif; ?>
            <?php if ($a['status'] === 'OTP_VERIFIED'): ?>
                <form method="post" action="<?= site_url('courier/shipments/' . (int)$a['id'] . '/proof') ?>" enctype="multipart/form-data" class="mt-2 row g-2">
                    <?= csrf_field() ?>
                    <div class="col-md-9"><input type="file" class="form-control form-control-sm" name="proof_image" accept="image/*" required></div>
                    <div class="col-md-3"><button class="btn btn-sm bm-btn-primary w-100">Upload Proof</button></div>
                </form>
            <?php endif; ?>
            <?php if ($a['status'] === 'PROOF_UPLOADED'): ?>
                <form method="post" action="<?= site_url('courier/shipments/' . (int)$a['id'] . '/complete') ?>" class="mt-2">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm bm-btn-primary">Complete Delivery</button>
                </form>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<div id="history" class="bm-card p-3">
    <h2 class="h6">Riwayat Delivery</h2>
    <table class="bm-table"><thead><tr><th>Shipment</th><th>Tanggal</th><th>Earning</th><th>Proof</th></tr></thead><tbody><?php foreach($history as $h): ?><tr><td><?= esc($h['shipment_number']) ?></td><td><?= esc($h['delivered_at'] ?? '-') ?></td><td><?= bm_currency((int)$h['courier_earning']) ?></td><td><?php if (!empty($h['proof_image'])): ?><a href="<?= esc(base_url($h['proof_image'])) ?>" target="_blank">Lihat</a><?php else: ?>-<?php endif; ?></td></tr><?php endforeach; ?></tbody></table>
</div>
<?= $this->endSection() ?>
