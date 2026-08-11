<?= $this->extend('layouts/umkm') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3">
    <h1 class="h5 mb-3"><?= $product ? 'Edit Produk' : 'Tambah Produk' ?></h1>
    <form method="post" action="<?= $product ? site_url('umkm/products/' . $product['id']) : site_url('umkm/products') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="bm-form-grid">
            <div class="bm-col-6"><label class="form-label">Toko</label><select name="store_id" class="form-select" required><?php foreach ($stores as $store): ?><option value="<?= (int)$store['id'] ?>" <?= (int)($product['store_id'] ?? 0) === (int)$store['id'] ? 'selected' : '' ?>><?= esc($store['name']) ?></option><?php endforeach; ?></select></div>
            <div class="bm-col-6"><label class="form-label">Kategori</label><select name="category_id" class="form-select" required><?php foreach ($categories as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (int)($product['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach; ?></select></div>
            <div class="bm-col-12"><label class="form-label">Nama Produk</label><input class="form-control" name="name" value="<?= esc($product['name'] ?? old('name') ?? '') ?>" required></div>
            <div class="bm-col-12"><label class="form-label">Deskripsi</label><textarea class="form-control" name="description" rows="3"><?= esc($product['description'] ?? '') ?></textarea></div>
            <div class="bm-col-4"><label class="form-label">Harga</label><input class="form-control" type="number" name="price" value="<?= esc($product['price'] ?? old('price') ?? 0) ?>" required></div>
            <div class="bm-col-4"><label class="form-label">Stok</label><input class="form-control" type="number" name="stock" value="<?= esc($product['stock'] ?? old('stock') ?? 0) ?>" required></div>
            <div class="bm-col-4"><label class="form-label">Berat (gram)</label><input class="form-control" type="number" name="weight" value="<?= esc($product['weight'] ?? old('weight') ?? 0) ?>"></div>
            <div class="bm-col-6"><label class="form-label">Status</label><select name="status" class="form-select"><option value="ACTIVE" <?= ($product['status'] ?? '') === 'ACTIVE' ? 'selected' : '' ?>>Active</option><option value="INACTIVE" <?= ($product['status'] ?? '') === 'INACTIVE' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="bm-col-6"><label class="form-label">Upload Gambar (multiple)</label><input type="file" class="form-control" name="images[]" multiple accept="image/*"></div>
        </div>

        <?php if (!empty($images)): ?>
            <div class="mt-3"><div class="small bm-muted mb-2">Pilih gambar utama</div><div class="d-flex flex-wrap gap-2"><?php foreach ($images as $img): ?><label class="border rounded p-2"><input type="radio" name="primary_image_id" value="<?= (int)$img['id'] ?>" <?= (int)$img['is_primary'] === 1 ? 'checked' : '' ?>> <img src="<?= esc(bm_image_url($img['file_path'], 'produk')) ?>" style="width:80px;height:80px;object-fit:cover" class="rounded"></label><?php endforeach; ?></div></div>
        <?php endif; ?>

        <button class="btn bm-btn-primary mt-3"><?= $product ? 'Update Produk' : 'Simpan Produk' ?></button>
    </form>
</div>
<?= $this->endSection() ?>
