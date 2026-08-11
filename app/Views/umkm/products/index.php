<?= $this->extend('layouts/umkm') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3"><h1 class="h5 mb-0">Produk Toko</h1><a href="<?= site_url('umkm/products/create') ?>" class="btn bm-btn-primary btn-sm">+ Tambah Produk</a></div>
<div class="bm-card p-3">
    <?php if (empty($products)): ?><div class="bm-empty">Belum ada produk. Mulai tambahkan produk untuk dijual.</div><?php else: ?>
    <div class="table-responsive"><table class="bm-table"><thead><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
    <?php foreach ($products as $p): ?>
        <tr>
            <td><div class="d-flex align-items-center gap-2"><img src="<?= esc(bm_image_url($p['primary_image'] ?? null, $p['name'])) ?>" style="width:48px;height:48px;object-fit:cover" class="rounded border"><div><div class="fw-semibold small"><?= esc($p['name']) ?></div><div class="small bm-muted"><?= esc($p['store_name']) ?></div></div></div></td>
            <td><?= esc($p['category_name']) ?></td>
            <td><?= bm_currency((int)$p['price']) ?></td>
            <td><?= (int)$p['stock'] ?></td>
            <td><span class="bm-status <?= bm_status_class($p['status']) ?>"><?= esc($p['status']) ?></span></td>
            <td>
                <div class="d-flex gap-1">
                    <a href="<?= site_url('umkm/products/' . (int)$p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form method="post" action="<?= site_url('umkm/products/' . (int)$p['id'] . '/toggle') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary">Toggle</button></form>
                    <form method="post" action="<?= site_url('umkm/products/' . (int)$p['id'] . '/delete') ?>" onsubmit="return confirm('Hapus produk?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Hapus</button></form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody></table></div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
