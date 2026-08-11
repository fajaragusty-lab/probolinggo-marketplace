<?php $store = $store ?? []; ?>
<a href="<?= site_url('store/' . ($store['slug'] ?? '')) ?>" class="bm-store-card">
    <img class="bm-avatar" src="<?= esc(bm_image_url($store['logo'] ?? null, $store['name'] ?? 'Store')) ?>" alt="<?= esc($store['name'] ?? 'Store') ?>">
    <div class="flex-grow-1">
        <div class="fw-semibold small"><?= esc($store['name'] ?? '-') ?></div>
        <div class="small bm-muted"><?= esc(($store['district'] ?? '') . ', ' . ($store['city'] ?? '')) ?></div>
        <div class="small bm-muted">★ <?= number_format((float) ($store['rating_avg'] ?? 0), 1) ?> · <?= (int) ($store['rating_count'] ?? 0) ?> ulasan</div>
    </div>
    <span class="bm-status is-success">Terverifikasi</span>
</a>
