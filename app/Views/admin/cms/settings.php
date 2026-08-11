<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-card p-3">
    <ul class="nav nav-pills bm-settings-tabs mb-3" role="tablist">
        <?php foreach (['general' => 'General', 'marketplace' => 'Marketplace', 'checkout' => 'Checkout', 'delivery' => 'Delivery', 'seo' => 'SEO', 'social' => 'Social', 'system' => 'System'] as $tab => $label): ?>
            <li class="nav-item" role="presentation"><button class="nav-link <?= $tab === 'general' ? 'active' : '' ?>" data-bs-toggle="pill" data-bs-target="#tab-<?= $tab ?>" type="button"><?= esc($label) ?></button></li>
        <?php endforeach; ?>
    </ul>

    <form method="post" action="<?= site_url('admin/settings') ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-general">
                <div class="row g-3">
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Branding</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">Application Name</label><input class="form-control" name="app_name" value="<?= esc($settings['app_name'] ?? '') ?>"></div><div class="bm-col-12"><label class="form-label">Tagline</label><input class="form-control" name="app_tagline" value="<?= esc($settings['app_tagline'] ?? '') ?>"></div><div class="bm-col-6"><label class="form-label">Logo</label><input type="file" class="form-control" name="logo_file" accept="image/*"></div><div class="bm-col-6"><label class="form-label">Favicon</label><input type="file" class="form-control" name="favicon_file" accept="image/*"></div></div></div></div>
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Regional</h2><div class="bm-form-grid"><div class="bm-col-6"><label class="form-label">Currency</label><input class="form-control" name="general_currency" value="<?= esc($settings['general_currency'] ?? 'IDR') ?>"></div><div class="bm-col-6"><label class="form-label">Timezone</label><input class="form-control" name="general_timezone" value="<?= esc($settings['general_timezone'] ?? 'Asia/Jakarta') ?>"></div><div class="bm-col-12"><label class="form-label">Contact Email</label><input class="form-control" name="contact_email" value="<?= esc($settings['contact_email'] ?? '') ?>"></div></div></div></div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-marketplace">
                <div class="row g-3">
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Marketplace Rules</h2><div class="bm-form-grid"><div class="bm-col-6"><label class="form-label">Minimum Order</label><input class="form-control" name="marketplace_minimum_order" value="<?= esc($settings['marketplace_minimum_order'] ?? '0') ?>"></div><div class="bm-col-6"><label class="form-label">Shipping Base Fee</label><input class="form-control" name="marketplace_shipping_base_fee" value="<?= esc($settings['marketplace_shipping_base_fee'] ?? '10000') ?>"></div><div class="bm-col-6"><label class="form-label">Seller Commission (%)</label><input class="form-control" name="marketplace_seller_commission" value="<?= esc($settings['marketplace_seller_commission'] ?? '5') ?>"></div></div></div></div>
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Approvals</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">UMKM Approval</label><select class="form-select" name="marketplace_umkm_approval"><option value="1" <?= (($settings['marketplace_umkm_approval'] ?? '1') === '1') ? 'selected' : '' ?>>Required</option><option value="0" <?= (($settings['marketplace_umkm_approval'] ?? '1') === '0') ? 'selected' : '' ?>>Auto approve</option></select></div><div class="bm-col-12"><label class="form-label">Product Approval</label><select class="form-select" name="marketplace_product_approval"><option value="1" <?= (($settings['marketplace_product_approval'] ?? '1') === '1') ? 'selected' : '' ?>>Required</option><option value="0" <?= (($settings['marketplace_product_approval'] ?? '1') === '0') ? 'selected' : '' ?>>Auto approve</option></select></div><div class="bm-col-12"><label class="form-label">Review Moderation</label><select class="form-select" name="marketplace_review_moderation"><option value="1" <?= (($settings['marketplace_review_moderation'] ?? '1') === '1') ? 'selected' : '' ?>>Moderated</option><option value="0" <?= (($settings['marketplace_review_moderation'] ?? '1') === '0') ? 'selected' : '' ?>>Auto publish</option></select></div></div></div></div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-checkout">
                <div class="row g-3">
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Payment Methods</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">Active payment codes</label><input class="form-control" name="checkout_payment_methods" value="<?= esc($settings['checkout_payment_methods'] ?? 'bank_transfer,qris') ?>" placeholder="bank_transfer,qris"></div><div class="bm-col-6"><label class="form-label">COD</label><select class="form-select" name="checkout_cod"><option value="0" <?= (($settings['checkout_cod'] ?? '0') === '0') ? 'selected' : '' ?>>Disabled</option><option value="1" <?= (($settings['checkout_cod'] ?? '0') === '1') ? 'selected' : '' ?>>Enabled</option></select></div><div class="bm-col-6"><label class="form-label">Order timeout (minutes)</label><input class="form-control" name="checkout_order_timeout" value="<?= esc($settings['checkout_order_timeout'] ?? '60') ?>"></div></div></div></div>
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Purchase Rules</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">Minimum Purchase</label><input class="form-control" name="checkout_minimum_purchase" value="<?= esc($settings['checkout_minimum_purchase'] ?? '0') ?>"></div></div></div></div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-delivery">
                <div class="row g-3">
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Courier Settings</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">Courier mode</label><input class="form-control" name="delivery_courier_mode" value="<?= esc($settings['delivery_courier_mode'] ?? 'auto') ?>"></div><div class="bm-col-12"><label class="form-label">Delivery fee label</label><input class="form-control" name="delivery_fee_label" value="<?= esc($settings['delivery_fee_label'] ?? 'Ongkir tetap dalam kota') ?>"></div></div></div></div>
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Coverage</h2><div class="bm-form-grid"><div class="bm-col-6"><label class="form-label">Radius (KM)</label><input class="form-control" name="delivery_radius_km" value="<?= esc($settings['delivery_radius_km'] ?? '15') ?>"></div><div class="bm-col-6"><label class="form-label">Assignment</label><input class="form-control" name="delivery_assignment" value="<?= esc($settings['delivery_assignment'] ?? 'nearest_available') ?>"></div></div></div></div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-seo">
                <div class="row g-3">
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Metadata</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">SEO Title</label><input class="form-control" name="seo_title" value="<?= esc($settings['seo_title'] ?? '') ?>"></div><div class="bm-col-12"><label class="form-label">Meta Description</label><textarea class="form-control" name="seo_meta_description" rows="3"><?= esc($settings['seo_meta_description'] ?? '') ?></textarea></div><div class="bm-col-12"><label class="form-label">Keywords</label><input class="form-control" name="seo_keywords" value="<?= esc($settings['seo_keywords'] ?? '') ?>"></div></div></div></div>
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Open Graph</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">OG Image</label><input type="file" class="form-control" name="og_image_file" accept="image/*"></div></div></div></div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-social">
                <div class="row g-3">
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Social Accounts</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">Facebook</label><input class="form-control" name="social_facebook" value="<?= esc($settings['social_facebook'] ?? '') ?>"></div><div class="bm-col-12"><label class="form-label">Instagram</label><input class="form-control" name="social_instagram" value="<?= esc($settings['social_instagram'] ?? '') ?>"></div><div class="bm-col-12"><label class="form-label">WhatsApp</label><input class="form-control" name="social_whatsapp" value="<?= esc($settings['social_whatsapp'] ?? '') ?>"></div><div class="bm-col-12"><label class="form-label">TikTok</label><input class="form-control" name="social_tiktok" value="<?= esc($settings['social_tiktok'] ?? '') ?>"></div></div></div></div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-system">
                <div class="row g-3">
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">System Control</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">Maintenance Mode</label><select class="form-select" name="maintenance_mode"><option value="0" <?= (($settings['maintenance_mode'] ?? '0') === '0') ? 'selected' : '' ?>>Disabled</option><option value="1" <?= (($settings['maintenance_mode'] ?? '0') === '1') ? 'selected' : '' ?>>Enabled</option></select></div><div class="bm-col-12"><label class="form-label">Cache</label><select class="form-select" name="system_cache_enabled"><option value="1" <?= (($settings['system_cache_enabled'] ?? '1') === '1') ? 'selected' : '' ?>>Enabled</option><option value="0" <?= (($settings['system_cache_enabled'] ?? '1') === '0') ? 'selected' : '' ?>>Disabled</option></select></div></div></div></div>
                    <div class="col-lg-6"><div class="bm-settings-card"><h2 class="h6 mb-3">Email / Contact</h2><div class="bm-form-grid"><div class="bm-col-12"><label class="form-label">System Contact Email</label><input class="form-control" name="contact_email" value="<?= esc($settings['contact_email'] ?? '') ?>"></div></div></div></div>
                </div>
            </div>
        </div>

        <button class="btn bm-btn-primary mt-3">Simpan Pengaturan</button>
    </form>
</div>
<?= $this->endSection() ?>
