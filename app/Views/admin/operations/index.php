<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="bm-page-intro mb-3">
    <div>
        <h2 class="h5 mb-1"><?= esc($title) ?></h2>
        <p class="bm-muted mb-0"><?= esc($description) ?></p>
    </div>
</div>

<div class="bm-card p-0 overflow-hidden">
    <?php if (empty($rows)): ?>
        <div class="bm-empty m-3">
            <div class="h6 mb-1"><?= esc($emptyTitle) ?></div>
            <p class="small bm-muted mb-0"><?= esc($emptyDescription) ?></p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="bm-table">
                <thead>
                <tr>
                    <?php foreach ($columns as $column): ?>
                        <th><?= esc($column) ?></th>
                    <?php endforeach; ?>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php foreach ($row as $cell): ?>
                            <td>
                                <?php if (($cell['type'] ?? 'text') === 'status'): ?>
                                    <span class="bm-status <?= bm_status_class((string) $cell['value']) ?>"><?= esc((string) $cell['value']) ?></span>
                                <?php elseif (($cell['type'] ?? 'text') === 'currency'): ?>
                                    <strong><?= bm_currency((int) $cell['value']) ?></strong>
                                <?php elseif (($cell['type'] ?? 'text') === 'media'): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="<?= esc(bm_image_url($cell['image'] ?? null, (string) $cell['value'])) ?>" alt="<?= esc((string) $cell['value']) ?>" class="bm-list-thumb">
                                        <div class="fw-semibold small"><?= esc((string) $cell['value']) ?></div>
                                    </div>
                                <?php else: ?>
                                    <div class="small"><?= esc((string) $cell['value']) ?></div>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
