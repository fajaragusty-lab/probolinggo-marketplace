<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Platform Dashboard</h1>
<div class="row g-3 mb-4">
    <?php foreach (['gmv'=>'GMV','revenue'=>'Revenue','total_orders'=>'Total Orders','pending_orders'=>'Pending Orders','completed_orders'=>'Completed Orders','cancelled_orders'=>'Cancelled Orders','total_customers'=>'Customers','total_umkm'=>'UMKM','total_products'=>'Products','total_couriers'=>'Couriers'] as $key => $label): ?>
    <div class="col-6 col-xl-3"><div class="card kpi shadow-sm"><div class="card-body"><div class="text-muted small"><?= esc($label) ?></div><div class="fw-bold fs-5"><?= number_format((int)($kpis[$key] ?? 0),0,',','.') ?></div></div></div></div>
    <?php endforeach; ?>
</div>

<div class="row g-3">
    <div class="col-lg-6">
        <div class="card shadow-sm"><div class="card-header">Orders Over Time</div><div class="card-body p-0">
            <table class="table table-sm mb-0"><thead><tr><th>Date</th><th>Orders</th><th>Revenue</th></tr></thead><tbody>
            <?php foreach ($ordersOverTime as $row): ?><tr><td><?= esc($row['d']) ?></td><td><?= (int)$row['total'] ?></td><td>Rp <?= number_format((int)$row['revenue'],0,',','.') ?></td></tr><?php endforeach; ?>
            </tbody></table>
        </div></div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm"><div class="card-header">Order Status Distribution</div><div class="card-body p-0">
            <table class="table table-sm mb-0"><thead><tr><th>Status</th><th>Total</th></tr></thead><tbody>
            <?php foreach ($statusDistribution as $row): ?><tr><td><?= esc($row['status']) ?></td><td><?= (int)$row['total'] ?></td></tr><?php endforeach; ?>
            </tbody></table>
        </div></div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header">Pending Actions</div><div class="card-body small">
        <div>UMKM verification pending: <strong><?= (int)$pendingUmkmCount ?></strong></div>
        <div>Courier verification pending: <strong><?= (int)$pendingCouriersCount ?></strong></div>
        <div>Product moderation pending: <strong><?= (int)$pendingProductsCount ?></strong></div>
        <div>Feedback moderation pending: <strong><?= (int)$pendingFeedbacksCount ?></strong></div>
    </div></div></div>
    <div class="col-lg-6"><div class="card shadow-sm"><div class="card-header">Top Products</div><div class="card-body p-0"><table class="table table-sm mb-0"><tbody>
        <?php foreach ($topProducts as $p): ?><tr><td><?= esc($p['name']) ?></td><td class="text-end"><?= (int)$p['sold_count'] ?> sold</td></tr><?php endforeach; ?>
    </tbody></table></div></div></div>
</div>
<?= $this->endSection() ?>
