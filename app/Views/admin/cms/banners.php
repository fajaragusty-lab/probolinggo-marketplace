<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<h1 class="h4 mb-3">Banner Management</h1>
<form method="post" action="<?= site_url('admin/banners') ?>" class="card shadow-sm p-3 mb-3">
    <?= csrf_field() ?>
    <div class="row g-2">
        <div class="col-md-4"><input class="form-control" name="title" placeholder="Title" required></div>
        <div class="col-md-4"><input class="form-control" name="subtitle" placeholder="Subtitle"></div>
        <div class="col-md-4"><input class="form-control" name="cta_label" placeholder="CTA Label"></div>
        <div class="col-md-4"><input class="form-control" name="cta_url" placeholder="https://..."></div>
        <div class="col-md-4"><input class="form-control" name="image" placeholder="Image path/url"></div>
        <div class="col-md-4"><input class="form-control" type="number" name="sort_order" value="0"></div>
        <div class="col-md-4"><input class="form-control" type="datetime-local" name="starts_at"></div>
        <div class="col-md-4"><input class="form-control" type="datetime-local" name="ends_at"></div>
        <div class="col-md-2 form-check ms-2 mt-2"><input class="form-check-input" name="is_active" type="checkbox" value="1" checked><label class="form-check-label">Active</label></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">Save</button></div>
    </div>
</form>
<table class="table table-sm bg-white shadow-sm"><thead><tr><th>Title</th><th>CTA</th><th>Order</th><th>Status</th></tr></thead><tbody>
<?php foreach($banners as $b): ?><tr><td><?= esc($b['title']) ?></td><td><?= esc($b['cta_url'] ?? '-') ?></td><td><?= (int)$b['sort_order'] ?></td><td><?= $b['is_active'] ? 'Active':'Inactive' ?></td></tr><?php endforeach; ?>
</tbody></table>
<?= $this->endSection() ?>
