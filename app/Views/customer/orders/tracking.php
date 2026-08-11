<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <div class="d-flex flex-wrap justify-content-between gap-2 align-items-center mb-3">
        <div>
            <h1 class="h5 mb-1">Tracking <?= esc($order['order_number']) ?></h1>
            <div class="small bm-muted">Pantau status operasional dan lokasi kurir terakhir.</div>
        </div>
        <a href="<?= site_url('orders/' . (int) $order['id']) ?>" class="btn btn-sm btn-outline-secondary">Kembali ke detail</a>
    </div>

    <?php foreach ($shipments as $shipment): ?>
        <div class="bm-card p-3 mb-3">
            <div class="d-flex justify-content-between gap-2 align-items-start mb-3">
                <div>
                    <div class="fw-semibold"><?= esc($shipment['shipment_number']) ?></div>
                    <div class="small bm-muted">Tujuan: <?= esc($shipment['delivery_address'] ?? '-') ?></div>
                    <?php if (!empty($shipment['courier_name'])): ?><div class="small bm-muted">Kurir: <?= esc($shipment['courier_name']) ?> · <?= esc($shipment['courier_phone'] ?? '-') ?></div><?php endif; ?>
                </div>
                <span class="bm-status <?= bm_status_class($shipment['status']) ?>"><?= esc($shipment['status']) ?></span>
            </div>

            <?php if (!empty($shipment['latest_tracking'])): ?>
                <div class="border rounded p-2 bg-light mb-3 small">
                    <div class="fw-semibold mb-1">📍 Lokasi Terakhir Kurir</div>
                    <div class="bm-muted">Lat: <?= esc($shipment['latest_tracking']['latitude']) ?></div>
                    <div class="bm-muted">Lng: <?= esc($shipment['latest_tracking']['longitude']) ?></div>
                    <?php if (!empty($shipment['latest_tracking']['accuracy'])): ?><div class="bm-muted">Akurasi: <?= esc(number_format((float)$shipment['latest_tracking']['accuracy'], 1)) ?> m</div><?php endif; ?>
                    <div class="bm-muted mt-1">Diperbarui: <?= esc($shipment['latest_tracking']['recorded_at']) ?></div>
                </div>
            <?php else: ?>
                <div class="alert alert-light border small">Tidak ada lokasi kurir terbaru.</div>
            <?php endif; ?>

            <div class="row g-2">
                <?php foreach ($shipment['timeline'] as $step): ?>
                    <div class="col-md-6">
                        <div class="border rounded p-2 h-100 <?= !empty($step['completed']) ? 'border-success' : '' ?>">
                            <div class="fw-semibold small"><?= esc($step['label']) ?></div>
                            <div class="small <?= !empty($step['completed']) ? 'text-success' : 'bm-muted' ?>">
                                <?= !empty($step['time']) ? esc($step['time']) : (!empty($step['completed']) ? 'Selesai' : 'Menunggu proses') ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
<?= $this->endSection() ?>
