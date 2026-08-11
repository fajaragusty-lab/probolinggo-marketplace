<?php
$product = $product ?? [];
$image = $product['primary_image'] ?? $product['image'] ?? null;
?>
<a href="<?= site_url('product/' . ($product['slug'] ?? '')) ?>" class="bm-product-card">
    <img src="<?= esc(bm_image_url($image, $product['name'] ?? 'Produk')) ?>" alt="<?= esc($product['name'] ?? 'Produk') ?>" class="bm-product-media">
    <div class="bm-product-body">
        <div class="small bm-muted text-truncate"><?= esc($product['store_name'] ?? '-') ?></div>
        <div class="fw-semibold small text-truncate"><?= esc($product['name'] ?? '-') ?></div>
        <div class="bm-price"><?= bm_currency((int) ($product['price'] ?? 0)) ?></div>
        <div class="small bm-muted">★ <?= number_format((float) ($product['rating_avg'] ?? 0), 1) ?> · <?= (int) ($product['sold_count'] ?? 0) ?> terjual</div>
        <div class="small <?= (int)($product['stock'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>"><?= (int)($product['stock'] ?? 0) > 0 ? 'Stok tersedia' : 'Stok habis' ?></div>
    </div>
</a>
