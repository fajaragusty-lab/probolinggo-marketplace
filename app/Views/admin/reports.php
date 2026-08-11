<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Reports</h1>
<form method="get" class="row g-2 mb-3">
    <div class="col-md-4"><input type="date" class="form-control" name="from" value="<?= esc($from) ?>"></div>
    <div class="col-md-4"><input type="date" class="form-control" name="to" value="<?= esc($to) ?>"></div>
    <div class="col-md-4"><button class="btn btn-primary w-100">Filter</button></div>
</form>
<div class="d-flex gap-2 mb-3">
    <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('admin/reports?from=' . urlencode($from) . '&to=' . urlencode($to) . '&export=csv&type=sales') ?>">Export Sales CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('admin/reports?from=' . urlencode($from) . '&to=' . urlencode($to) . '&export=csv&type=payments') ?>">Export Payment CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('admin/reports?from=' . urlencode($from) . '&to=' . urlencode($to) . '&export=csv&type=shipments') ?>">Export Shipment CSV</a>
</div>
<div class="row g-3">
<div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Sales Orders</div><div class="h4 mb-0"><?= count($sales) ?></div></div></div></div>
<div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Payment Rows</div><div class="h4 mb-0"><?= count($payments) ?></div></div></div></div>
<div class="col-lg-4"><div class="card shadow-sm"><div class="card-body"><div class="text-muted small">Shipment Rows</div><div class="h4 mb-0"><?= count($shipments) ?></div></div></div></div>
</div>
<?= $this->endSection() ?>
