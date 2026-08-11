<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3 mb-3">
    <form method="get" class="row g-2">
        <div class="col-md-4"><label class="form-label small bm-muted">From</label><input type="date" class="form-control" name="from" value="<?= esc($from) ?>"></div>
        <div class="col-md-4"><label class="form-label small bm-muted">To</label><input type="date" class="form-control" name="to" value="<?= esc($to) ?>"></div>
        <div class="col-md-4 d-flex align-items-end"><button class="btn bm-btn-primary w-100">Filter</button></div>
    </form>
</div>
<div class="d-flex flex-wrap gap-2 mb-3">
    <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('admin/reports?from=' . urlencode($from) . '&to=' . urlencode($to) . '&export=csv&type=sales') ?>">Export Sales CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('admin/reports?from=' . urlencode($from) . '&to=' . urlencode($to) . '&export=csv&type=payments') ?>">Export Payments CSV</a>
    <a class="btn btn-outline-secondary btn-sm" href="<?= site_url('admin/reports?from=' . urlencode($from) . '&to=' . urlencode($to) . '&export=csv&type=shipments') ?>">Export Shipments CSV</a>
</div>
<div class="bm-kpi-grid"><div class="bm-kpi"><div class="small bm-muted">Sales Orders</div><div class="h5 mb-0"><?= (int)$salesCount ?></div></div><div class="bm-kpi"><div class="small bm-muted">Payment Rows</div><div class="h5 mb-0"><?= (int)$paymentsCount ?></div></div><div class="bm-kpi"><div class="small bm-muted">Shipment Rows</div><div class="h5 mb-0"><?= (int)$shipmentsCount ?></div></div></div>
<?= $this->endSection() ?>
