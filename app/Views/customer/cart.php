<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3">
    <h1 class="h5 mb-3">Keranjang</h1>
    <?php if (empty($items)): ?>
        <div class="text-center text-muted py-5">
            <div style="font-size:48px">🛒</div>
            <p>Keranjang masih kosong</p>
            <a href="<?= site_url('/') ?>" class="btn btn-bm">Belanja sekarang</a>
        </div>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
        <div class="card mb-2 border-0 shadow-sm">
            <div class="card-body p-3">
                <div class="fw-semibold"><?= esc($item['product_name']) ?></div>
                <div class="small text-muted"><?= esc($item['store_name']) ?></div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <div class="price">Rp <?= number_format($item['price'], 0, ',', '.') ?></div>
                    <form action="<?= site_url('cart/update') ?>" method="post" class="d-flex gap-1 align-items-center">
                        <?= csrf_field() ?>
                        <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                        <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$item['stock'] ?>" class="form-control form-control-sm" style="width:70px">
                        <button class="btn btn-sm btn-outline-primary">Ubah</button>
                    </form>
                </div>
                <div class="d-flex justify-content-between mt-1">
                    <span class="small">Subtotal: Rp <?= number_format($item['line_total'], 0, ',', '.') ?></span>
                    <form action="<?= site_url('cart/remove') ?>" method="post" onsubmit="return confirm('Hapus item?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                        <button class="btn btn-sm btn-link text-danger p-0">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <div class="card border-0 shadow-sm mt-3">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <strong>Rp <?= number_format($subtotal, 0, ',', '.') ?></strong>
                </div>
                <a href="<?= site_url('checkout') ?>" class="btn btn-bm w-100 mt-3">Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
