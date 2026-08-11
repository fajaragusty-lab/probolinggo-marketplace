<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $editing = $editBanner ?? null; ?>
<div class="bm-layout-split mb-3">
    <div class="bm-card p-3">
        <h2 class="h6 mb-3"><?= $editing ? 'Edit Banner' : 'Buat Banner Baru' ?></h2>
        <form method="post" action="<?= site_url('admin/banners') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
            <div class="bm-form-grid">
                <div class="bm-col-12"><label class="form-label">Judul</label><input id="bannerTitleInput" class="form-control" name="title" value="<?= esc($editing['title'] ?? old('title') ?? '') ?>" required></div>
                <div class="bm-col-12"><label class="form-label">Deskripsi</label><input id="bannerSubtitleInput" class="form-control" name="subtitle" value="<?= esc($editing['subtitle'] ?? old('subtitle') ?? '') ?>"></div>
                <div class="bm-col-6"><label class="form-label">Label CTA</label><input id="bannerCtaInput" class="form-control" name="cta_label" value="<?= esc($editing['cta_label'] ?? old('cta_label') ?? '') ?>"></div>
                <div class="bm-col-6"><label class="form-label">URL CTA</label><input class="form-control" name="cta_url" value="<?= esc($editing['cta_url'] ?? old('cta_url') ?? '') ?>" placeholder="https://... atau /route"></div>
                <div class="bm-col-6">
                    <label class="form-label">Desktop banner</label>
                    <div class="bm-banner-dropzone">
                        <input id="bannerImageInput" type="file" class="form-control" name="image_file" accept="image/*">
                        <div class="small bm-muted mt-2">Upload JPG/PNG/WEBP untuk desktop hero.</div>
                        <?php if (!empty($editing['image'])): ?><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_image" value="1" id="removeDesktop"><label class="form-check-label" for="removeDesktop">Hapus gambar desktop</label></div><?php endif; ?>
                    </div>
                </div>
                <div class="bm-col-6">
                    <label class="form-label">Mobile banner</label>
                    <div class="bm-banner-dropzone">
                        <input id="bannerMobileImageInput" type="file" class="form-control" name="mobile_image_file" accept="image/*">
                        <div class="small bm-muted mt-2">Upload versi mobile agar tampilan lebih optimal.</div>
                        <?php if (!empty($editing['mobile_image'])): ?><div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="remove_mobile_image" value="1" id="removeMobile"><label class="form-check-label" for="removeMobile">Hapus gambar mobile</label></div><?php endif; ?>
                    </div>
                </div>
                <div class="bm-col-4"><label class="form-label">Urutan</label><input type="number" class="form-control" name="sort_order" value="<?= esc($editing['sort_order'] ?? old('sort_order') ?? 0) ?>"></div>
                <div class="bm-col-4"><label class="form-label">Mulai</label><input type="datetime-local" class="form-control" name="starts_at" value="<?= !empty($editing['starts_at']) ? date('Y-m-d\TH:i', strtotime($editing['starts_at'])) : '' ?>"></div>
                <div class="bm-col-4"><label class="form-label">Selesai</label><input type="datetime-local" class="form-control" name="ends_at" value="<?= !empty($editing['ends_at']) ? date('Y-m-d\TH:i', strtotime($editing['ends_at'])) : '' ?>"></div>
                <div class="bm-col-12 form-check"><input class="form-check-input" name="is_active" type="checkbox" value="1" id="activeCheck" <?= (int)($editing['is_active'] ?? 1) === 1 ? 'checked' : '' ?>><label class="form-check-label" for="activeCheck">Publish sekarang</label></div>
            </div>
            <div class="d-flex gap-2 mt-3">
                <button class="btn bm-btn-primary"><?= $editing ? 'Update Banner' : 'Simpan Banner' ?></button>
                <?php if ($editing): ?><a href="<?= site_url('admin/banners') ?>" class="btn btn-outline-secondary">Batal Edit</a><?php endif; ?>
            </div>
        </form>
    </div>
    <div class="bm-preview-stack">
        <?php $desktopSrc = bm_image_url($editing['image'] ?? null, 'Desktop Banner'); ?>
        <?php $mobileSrc = bm_image_url($editing['mobile_image'] ?? ($editing['image'] ?? null), 'Mobile Banner'); ?>
        <div class="bm-card p-3">
            <h2 class="h6 mb-3">Desktop Preview</h2>
            <div class="bm-banner-preview">
                <img id="bannerPreviewDesktop" src="<?= esc($desktopSrc) ?>" style="height:220px" alt="desktop preview">
                <div class="bm-banner-overlay">
                    <div id="bannerPreviewTitle" class="fw-semibold"><?= esc($editing['title'] ?? 'Judul banner') ?></div>
                    <div id="bannerPreviewSubtitle" class="small opacity-75"><?= esc($editing['subtitle'] ?? 'Deskripsi banner') ?></div>
                    <span id="bannerPreviewCta" class="badge bg-light text-dark mt-2"><?= esc($editing['cta_label'] ?? 'CTA') ?></span>
                </div>
            </div>
        </div>
        <div class="bm-card p-3">
            <h2 class="h6 mb-3">Mobile Preview</h2>
            <div class="bm-banner-preview">
                <img id="bannerPreviewMobile" src="<?= esc($mobileSrc) ?>" style="height:160px" alt="mobile preview">
            </div>
        </div>
    </div>
</div>

<div class="bm-card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="h6 mb-1">Daftar Banner</h2>
            <div class="small bm-muted">Gunakan status publish, penjadwalan, dan duplicate untuk mengelola kampanye.</div>
        </div>
    </div>
    <?php if (empty($banners)): ?>
        <div class="bm-empty">
            <h3 class="h6 mb-1">No banners yet</h3>
            <p class="small bm-muted mb-0">Buat hero banner pertama untuk menghidupkan homepage customer.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="bm-table">
                <thead><tr><th>Preview</th><th>Konten</th><th>Jadwal</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach($banners as $b): ?>
                    <tr>
                        <td><img src="<?= esc(bm_image_url($b['image'] ?? null, $b['title'])) ?>" alt="<?= esc($b['title']) ?>" style="width:140px;height:74px;object-fit:cover" class="rounded-3 border"></td>
                        <td><strong><?= esc($b['title']) ?></strong><div class="small bm-muted"><?= esc($b['subtitle'] ?? '-') ?></div><div class="small"><?= esc($b['cta_label'] ?? '-') ?> · <?= esc($b['cta_url'] ?? '-') ?></div></td>
                        <td class="small bm-muted"><?= esc($b['starts_at'] ?? '-') ?><br><?= esc($b['ends_at'] ?? '-') ?></td>
                        <td><span class="bm-status <?= bm_status_class($b['is_active'] ? 'ACTIVE' : 'INACTIVE') ?>"><?= $b['is_active'] ? 'Published' : 'Draft' ?></span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/banners?edit=' . (int)$b['id']) ?>">Edit</a>
                                <form method="post" action="<?= site_url('admin/banners/' . (int)$b['id'] . '/toggle') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary"><?= $b['is_active'] ? 'Unpublish' : 'Publish' ?></button></form>
                                <form method="post" action="<?= site_url('admin/banners/' . (int)$b['id'] . '/duplicate') ?>"><?= csrf_field() ?><button class="btn btn-sm btn-outline-secondary">Duplicate</button></form>
                                <form method="post" action="<?= site_url('admin/banners/' . (int)$b['id'] . '/delete') ?>" onsubmit="return confirm('Hapus banner ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
