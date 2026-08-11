<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3">
    <a href="javascript:history.back()" class="btn btn-sm btn-outline-secondary mb-2">← Kembali</a>
    <div class="card product-card mb-3">
        <div class="bg-light d-flex align-items-center justify-content-center" style="height:220px;font-size:64px">📦</div>
        <div class="card-body">
            <div class="small text-muted">
                <a href="<?= site_url('store/' . $product['store_slug']) ?>"><?= esc($product['store_name']) ?></a>
                · <?= esc($product['category_name']) ?>
            </div>
            <h1 class="h5 mt-1"><?= esc($product['name']) ?></h1>
            <div class="price fs-4">Rp <?= number_format($product['price'], 0, ',', '.') ?></div>
            <div class="small text-muted mb-2">★ <?= number_format($product['rating_avg'], 1) ?> (<?= $product['rating_count'] ?> ulasan) · Stok <?= $product['stock'] ?></div>
            <p class="small"><?= nl2br(esc($product['description'] ?? '')) ?></p>

            <?php if (session()->get('user_id') && in_array('customer', session()->get('roles') ?? [], true)): ?>
            <form action="<?= site_url('cart/add') ?>" method="post" class="d-flex gap-2 align-items-center">
                <?= csrf_field() ?>
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" class="form-control" style="width:80px">
                <button type="submit" class="btn btn-bm flex-grow-1" <?= $product['stock'] < 1 ? 'disabled' : '' ?>>
                    + Keranjang
                </button>
            </form>
            <?php else: ?>
            <a href="<?= site_url('login') ?>" class="btn btn-bm w-100">Login untuk beli</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
