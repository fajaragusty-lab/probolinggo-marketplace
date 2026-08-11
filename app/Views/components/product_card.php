<?php
$product = $product ?? [];
$image = $product['primary_image'] ?? $product['image'] ?? null;
$isCustomer = session()->get('user_id') && in_array('customer', session()->get('roles') ?? [], true);
?>
<div class="bm-product-card">
    <a href="<?= site_url('product/' . ($product['slug'] ?? '')) ?>" class="text-decoration-none text-reset">
        <img src="<?= esc(bm_image_url($image, $product['name'] ?? 'Produk')) ?>" alt="<?= esc($product['name'] ?? 'Produk') ?>" class="bm-product-media">
    </a>
    <div class="bm-product-body">
        <div class="small bm-muted text-truncate"><?= esc($product['store_name'] ?? '-') ?></div>
        <a href="<?= site_url('product/' . ($product['slug'] ?? '')) ?>" class="fw-semibold small text-decoration-none text-reset bm-product-title"><?= esc($product['name'] ?? '-') ?></a>
        <div class="bm-price"><?= bm_currency((int) ($product['price'] ?? 0)) ?></div>
        <div class="small bm-muted">★ <?= number_format((float) ($product['rating_avg'] ?? 0), 1) ?> · <?= (int) ($product['sold_count'] ?? 0) ?> terjual</div>
        <div class="small <?= (int)($product['stock'] ?? 0) > 0 ? 'text-success' : 'text-danger' ?>"><?= (int)($product['stock'] ?? 0) > 0 ? 'Stok tersedia' : 'Stok habis' ?></div>

        <div class="bm-product-actions">
            <a href="<?= site_url('product/' . ($product['slug'] ?? '')) ?>" class="btn btn-sm btn-outline-secondary flex-grow-1">Detail</a>
            <?php if ($isCustomer): ?>
                <form action="<?= site_url('cart/add') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <input type="hidden" name="product_id" value="<?= (int) ($product['id'] ?? 0) ?>">
                    <input type="hidden" name="quantity" value="1">
                    <button class="btn btn-sm bm-btn-primary" type="submit" <?= (int)($product['stock'] ?? 0) < 1 ? 'disabled' : '' ?>><i class="bi bi-cart-plus"></i></button>
                </form>
            <?php else: ?>
                <a href="<?= site_url('login') ?>" class="btn btn-sm bm-btn-primary"><i class="bi bi-cart-plus"></i></a>
            <?php endif; ?>
        </div>
    </div>
</div>
