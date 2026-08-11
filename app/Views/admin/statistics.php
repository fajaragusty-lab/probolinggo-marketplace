<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3 mb-3">
    <form class="row g-2" method="get">
        <div class="col-md-3"><label class="form-label small bm-muted">Range</label><select class="form-select" name="range"><?php foreach (['today'=>'Today','yesterday'=>'Yesterday','last7'=>'Last 7 Days','last30'=>'Last 30 Days','this_month'=>'This Month','last_month'=>'Last Month','custom'=>'Custom'] as $k => $v): ?><option value="<?= $k ?>" <?= $preset === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
        <div class="col-md-3"><label class="form-label small bm-muted">From</label><input type="date" class="form-control" name="from" value="<?= esc($range['from']) ?>"></div>
        <div class="col-md-3"><label class="form-label small bm-muted">To</label><input type="date" class="form-control" name="to" value="<?= esc($range['to']) ?>"></div>
        <div class="col-md-3 d-flex align-items-end"><button class="btn bm-btn-primary w-100">Apply</button></div>
    </form>
</div>
<div class="bm-kpi-grid mb-3"><?php foreach ($kpis as $label => $value): ?><div class="bm-kpi"><div class="small bm-muted"><?= esc(ucwords(str_replace('_',' ',$label))) ?></div><div class="h5 mb-0"><?= number_format((int)$value,0,',','.') ?></div></div><?php endforeach; ?></div>
<div class="row g-3"><div class="col-lg-6"><div class="bm-card p-3"><h2 class="h6">Top Stores</h2><table class="bm-table"><tbody><?php foreach ($topStores as $s): ?><tr><td><?= esc($s['name']) ?></td><td class="text-end"><?= bm_currency((int)$s['sales']) ?></td></tr><?php endforeach; ?></tbody></table></div></div><div class="col-lg-6"><div class="bm-card p-3"><h2 class="h6">Top Products</h2><table class="bm-table"><tbody><?php foreach ($topProducts as $p): ?><tr><td><?= esc($p['name']) ?></td><td class="text-end"><?= (int)$p['sold_count'] ?> sold</td></tr><?php endforeach; ?></tbody></table></div></div></div>
<?= $this->endSection() ?>
