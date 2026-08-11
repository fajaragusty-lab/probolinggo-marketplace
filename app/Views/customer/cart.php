<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <h1 class="h5 mb-3">Keranjang Belanja</h1>
    <?php if (empty($items)): ?>
        <div class="bm-empty"><div class="h2 mb-2">🛒</div><h2 class="h6 mb-1">Keranjang masih kosong</h2><p class="small bm-muted mb-3">Tambahkan produk favorit untuk mulai checkout.</p><a href="<?= site_url('/') ?>" class="btn bm-btn-primary">Mulai Belanja</a></div>
    <?php else: ?>
        <div class="row g-3">
            <div class="col-lg-8">
                <?php foreach ($items as $item): ?>
                    <div class="bm-card p-3 mb-2">
                        <div class="d-flex gap-3">
                            <img src="<?= esc(bm_image_url($item['primary_image'] ?? null, $item['product_name'])) ?>" alt="<?= esc($item['product_name']) ?>" style="width:88px;height:88px;object-fit:cover" class="rounded border">
                            <div class="flex-grow-1">
                                <div class="fw-semibold"><?= esc($item['product_name']) ?></div>
                                <div class="small bm-muted mb-1"><?= esc($item['store_name']) ?></div>
                                <div class="bm-price mb-2"><?= bm_currency((int)$item['price']) ?></div>
                                <div class="d-flex flex-wrap gap-2 justify-content-between align-items-end">
                                    <form action="<?= site_url('cart/update') ?>" method="post" class="d-flex gap-2 align-items-end">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                                        <div><label class="form-label small bm-muted">Qty</label><input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$item['stock'] ?>" class="form-control"></div>
                                        <button class="btn btn-outline-primary">Update</button>
                                    </form>
                                    <form action="<?= site_url('cart/remove') ?>" method="post" onsubmit="return confirm('Hapus item dari keranjang?')">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="item_id" value="<?= (int)$item['id'] ?>">
                                        <button class="btn btn-link text-danger p-0">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="col-lg-4">
                <div class="bm-card p-3">
                    <h2 class="h6">Ringkasan Belanja</h2>
                    <div class="d-flex justify-content-between small mb-1"><span>Subtotal</span><strong><?= bm_currency((int)$subtotal) ?></strong></div>
                    <div class="d-flex justify-content-between small mb-1"><span>Diskon</span><span><?= bm_currency(0) ?></span></div>
                    <div class="d-flex justify-content-between small mb-2"><span>Estimasi Ongkir</span><span>Ditentukan saat checkout</span></div>
                    <hr>
                    <a href="<?= site_url('checkout') ?>" class="btn bm-btn-primary w-100">Lanjut Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
