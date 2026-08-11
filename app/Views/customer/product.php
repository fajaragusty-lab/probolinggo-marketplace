<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-3 py-md-4">
    <div class="row g-3">
        <div class="col-lg-8">
            <div class="bm-card p-3 p-md-4 mb-3">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <?php $main = $product['primary_image'] ?? null; ?>
                        <img id="productMainImage" src="<?= esc(bm_image_url($main, $product['name'])) ?>" class="w-100 rounded-4 border" style="aspect-ratio:1/1;object-fit:cover" alt="<?= esc($product['name']) ?>">
                        <?php if (!empty($product['images'])): ?>
                            <div class="bm-thumb-list mt-3">
                                <?php foreach ($product['images'] as $index => $img): ?>
                                    <img src="<?= esc(bm_image_url($img['file_path'], $product['name'])) ?>" class="bm-thumb" data-thumb="<?= esc(bm_image_url($img['file_path'], $product['name'])) ?>" data-target="productMainImage" alt="thumb <?= $index + 1 ?>">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-lg-7">
                        <div class="small bm-muted mb-2 d-flex flex-wrap gap-2 align-items-center">
                            <a href="<?= site_url('store/' . $product['store_slug']) ?>" class="text-decoration-none"><?= esc($product['store_name']) ?></a>
                            <span>·</span>
                            <span><?= esc($product['category_name']) ?></span>
                            <?php if ((int) ($product['is_verified'] ?? 0) === 1): ?>
                                <span class="bm-status is-success">Seller Terverifikasi</span>
                            <?php endif; ?>
                        </div>
                        <h1 class="h3 mb-2"><?= esc($product['name']) ?></h1>
                        <div class="d-flex flex-wrap gap-2 small bm-muted mb-3"><span>★ <?= number_format((float)$product['rating_avg'], 1) ?></span><span><?= (int)$product['rating_count'] ?> ulasan</span><span><?= (int)$product['sold_count'] ?> terjual</span></div>
                        <div class="h2 fw-bold mb-2"><?= bm_currency((int)$product['price']) ?></div>
                        <div class="mb-3"><span class="bm-status <?= bm_status_class((int)$product['stock'] > 0 ? 'ACTIVE' : 'INACTIVE') ?>"><?= (int)$product['stock'] > 0 ? 'Stok tersedia' : 'Stok habis' ?></span></div>

                        <div class="bm-card p-3 mb-3">
                            <div class="fw-semibold mb-2">Informasi Toko</div>
                            <div class="small"><?= esc($product['store_name']) ?></div>
                            <div class="small bm-muted"><?= esc(($product['store_district'] ?? '') . ', ' . ($product['store_city'] ?? '')) ?></div>
                            <div class="small bm-muted">Pengiriman dari: <?= esc($product['store_address'] ?? '-') ?></div>
                        </div>

                        <?php if (session()->get('user_id') && in_array('customer', session()->get('roles') ?? [], true)): ?>
                            <form action="<?= site_url('cart/add') ?>" method="post" class="d-flex flex-wrap gap-2 align-items-end">
                                <?= csrf_field() ?>
                                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                                <div>
                                    <label class="form-label small bm-muted">Jumlah</label>
                                    <input type="number" name="quantity" value="1" min="1" max="<?= (int)$product['stock'] ?>" class="form-control" style="width:120px">
                                </div>
                                <button class="btn bm-btn-primary" type="submit" <?= (int)$product['stock'] < 1 ? 'disabled' : '' ?>><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</button>
                                <button class="btn btn-outline-primary" type="submit" name="buy_now" value="1" <?= (int)$product['stock'] < 1 ? 'disabled' : '' ?>>Beli Sekarang</button>
                            </form>
                        <?php else: ?>
                            <a href="<?= site_url('login') ?>" class="btn bm-btn-primary">Login untuk beli</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="bm-card p-3 mb-3">
                <h2 class="h6 mb-2">Deskripsi Produk</h2>
                <div class="small"><?= nl2br(esc($product['description'] ?? 'Belum ada deskripsi.')) ?></div>
            </div>

            <div class="bm-card p-3 mb-3">
                <h2 class="h6 mb-3">Spesifikasi</h2>
                <div class="row g-2 small">
                    <div class="col-md-6"><strong>Kategori</strong><div class="bm-muted"><?= esc($product['category_name']) ?></div></div>
                    <div class="col-md-6"><strong>Stok</strong><div class="bm-muted"><?= (int) $product['stock'] ?> unit</div></div>
                    <div class="col-md-6"><strong>Berat</strong><div class="bm-muted"><?= (int) ($product['weight'] ?? 0) ?> gram</div></div>
                    <div class="col-md-6"><strong>Asal Pengiriman</strong><div class="bm-muted"><?= esc($product['store_city'] ?? 'Probolinggo') ?></div></div>
                </div>
            </div>

            <div class="bm-card p-3">
                <h2 class="h6 mb-2">Ulasan Pembeli</h2>
                <?php if (empty($reviews)): ?>
                    <div class="bm-empty py-3">Belum ada ulasan untuk produk ini.</div>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="border-bottom py-2">
                            <div class="small fw-semibold"><?= esc($review['customer_name']) ?> <span class="bm-muted">· ★ <?= (int)$review['rating'] ?></span></div>
                            <div class="small bm-muted"><?= esc($review['comment'] ?? '-') ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="bm-card p-3 mb-3 position-sticky" style="top:96px">
                <div class="small bm-muted">Ringkasan belanja</div>
                <div class="h4 fw-bold mb-2"><?= bm_currency((int)$product['price']) ?></div>
                <div class="small bm-muted mb-3">Produk siap dikirim dari toko lokal Probolinggo.</div>
                <div class="d-grid gap-2">
                    <a href="#productMainImage" class="btn btn-outline-secondary">Lihat galeri</a>
                    <a href="<?= site_url('store/' . $product['store_slug']) ?>" class="btn btn-outline-primary">Kunjungi toko</a>
                </div>
            </div>

            <div class="bm-card p-3">
                <h2 class="h6 mb-2">Produk Terkait</h2>
                <div class="row g-2">
                    <?php if (empty($related)): ?><div class="small bm-muted">Belum ada rekomendasi.</div><?php endif; ?>
                    <?php foreach ($related as $item): ?><div class="col-6 col-lg-12"><?= view('components/product_card', ['product' => $item]) ?></div><?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
