<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3">
    <form method="post" action="<?= site_url('admin/settings') ?>">
        <?= csrf_field() ?>
        <div class="bm-form-grid">
            <div class="bm-col-6"><label class="form-label">Application Name</label><input class="form-control" name="app_name" value="<?= esc($settings['app_name'] ?? '') ?>"></div>
            <div class="bm-col-6"><label class="form-label">Tagline</label><input class="form-control" name="app_tagline" value="<?= esc($settings['app_tagline'] ?? '') ?>"></div>
            <div class="bm-col-4"><label class="form-label">Currency</label><input class="form-control" name="default_currency" value="<?= esc($settings['default_currency'] ?? 'IDR') ?>"></div>
            <div class="bm-col-4"><label class="form-label">Shipping Base Fee</label><input class="form-control" name="shipping_base_fee" value="<?= esc($settings['shipping_base_fee'] ?? '10000') ?>"></div>
            <div class="bm-col-4"><label class="form-label">Minimum Order</label><input class="form-control" name="minimum_order" value="<?= esc($settings['minimum_order'] ?? '0') ?>"></div>
            <div class="bm-col-6"><label class="form-label">Contact Email</label><input class="form-control" name="contact_email" value="<?= esc($settings['contact_email'] ?? '') ?>"></div>
            <div class="bm-col-6"><label class="form-label">Maintenance Mode</label><select class="form-select" name="maintenance_mode"><option value="0" <?= (($settings['maintenance_mode'] ?? '0') === '0') ? 'selected' : '' ?>>Disabled</option><option value="1" <?= (($settings['maintenance_mode'] ?? '0') === '1') ? 'selected' : '' ?>>Enabled</option></select></div>
        </div>
        <button class="btn bm-btn-primary mt-3">Simpan Pengaturan</button>
    </form>
</div>
<?= $this->endSection() ?>
