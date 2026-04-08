<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Activity Log</h1><p><span class="badge bg-secondary"><?= count($logs) ?> entries</span></p></div>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>Waktu</th><th>User</th><th>Role</th><th>Aksi</th><th>Tabel</th><th>Detail</th></tr></thead>
                <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:1.5rem"></i><br>Belum ada aktivitas</td></tr>
                <?php else: foreach ($logs as $log): ?>
                    <tr>
                        <td data-label="Waktu"><code style="font-size:0.72rem"><?= date('d/m/y H:i', strtotime($log['created_at'])) ?></code></td>
                        <td data-label="User" class="fw-semibold"><?= esc($log['username']) ?></td>
                        <td data-label="Role"><span class="badge bg-secondary" style="font-size:0.65rem"><?= $log['role'] ?></span></td>
                        <td data-label="Aksi"><?= esc($log['aksi']) ?></td>
                        <td data-label="Tabel"><code><?= esc($log['tabel'] ?? '-') ?></code></td>
                        <td data-label="Detail" style="max-width:150px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc($log['detail'] ?? '') ?>"><?= esc(mb_strimwidth($log['detail'] ?? '-', 0, 50, '...')) ?></td>
                    </tr>
                <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if (isset($pager)): ?>
    <div class="card-footer d-flex justify-content-center"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
