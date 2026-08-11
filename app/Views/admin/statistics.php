<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Statistics</h1>
<form class="row g-2 mb-3" method="get">
    <div class="col-md-3"><select class="form-select" name="range">
        <?php foreach (['today'=>'Today','yesterday'=>'Yesterday','last7'=>'Last 7 Days','last30'=>'Last 30 Days','this_month'=>'This Month','last_month'=>'Last Month','custom'=>'Custom'] as $k => $v): ?>
            <option value="<?= $k ?>" <?= $preset === $k ? 'selected' : '' ?>><?= $v ?></option>
        <?php endforeach; ?>
    </select></div>
    <div class="col-md-3"><input type="date" class="form-control" name="from" value="<?= esc($range['from']) ?>"></div>
    <div class="col-md-3"><input type="date" class="form-control" name="to" value="<?= esc($range['to']) ?>"></div>
    <div class="col-md-3"><button class="btn btn-primary w-100">Apply</button></div>
</form>
<div class="row g-3 mb-3">
<?php foreach ($kpis as $label => $value): ?><div class="col-6 col-lg-3"><div class="card shadow-sm"><div class="card-body"><div class="small text-muted"><?= esc(ucwords(str_replace('_',' ',$label))) ?></div><div class="fw-semibold"><?= number_format((int)$value,0,',','.') ?></div></div></div></div><?php endforeach; ?>
</div>
<div class="row g-3">
<div class="col-lg-6"><div class="card shadow-sm"><div class="card-header">Top Stores</div><table class="table table-sm mb-0"><tbody><?php foreach ($topStores as $s): ?><tr><td><?= esc($s['name']) ?></td><td class="text-end">Rp <?= number_format((int)$s['sales'],0,',','.') ?></td></tr><?php endforeach; ?></tbody></table></div></div>
<div class="col-lg-6"><div class="card shadow-sm"><div class="card-header">Top Products</div><table class="table table-sm mb-0"><tbody><?php foreach ($topProducts as $p): ?><tr><td><?= esc($p['name']) ?></td><td class="text-end"><?= (int)$p['sold_count'] ?> sold</td></tr><?php endforeach; ?></tbody></table></div></div>
</div>
<?= $this->endSection() ?>
