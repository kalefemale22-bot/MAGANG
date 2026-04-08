<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Manage Users</h1><p><span class="badge bg-secondary"><?= count($users) ?> users</span></p></div>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>Username</th><th>Nama</th><th>Role</th><th>Status</th><th>Last Login</th><th>Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                <tr class="<?= !$u['is_active'] ? 'table-secondary' : '' ?>">
                    <td data-label="Username"><code><?= esc($u['username']) ?></code></td>
                    <td data-label="Nama" class="fw-semibold"><?= esc($u['nama'] ?? '-') ?></td>
                    <td data-label="Role"><span class="badge bg-secondary" style="font-size:0.65rem"><?= $u['role'] ?></span></td>
                    <td data-label="Status">
                        <?php if ($u['is_active']): ?>
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i></span>
                        <?php else: ?>
                        <span class="badge bg-secondary"><i class="bi bi-x-circle"></i></span>
                        <?php endif; ?>
                    </td>
                    <td data-label="Last Login" style="font-size:0.75rem;color:#64748b"><?= $u['last_login'] ? date('d/m/y H:i', strtotime($u['last_login'])) : '-' ?></td>
                    <td data-label="Aksi">
                        <?php if ($u['id'] !== 0): ?>
                        <div class="d-flex gap-1">
                            <form action="/sys/users/toggle/<?= $u['id'] ?>" method="post"><?= csrf_field() ?><button class="btn btn-sm btn-outline-<?= $u['is_active'] ? 'warning' : 'success' ?>" title="<?= $u['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>"><i class="bi bi-<?= $u['is_active'] ? 'pause-circle' : 'play-circle' ?>"></i></button></form>
                            <form action="/sys/users/reset-password/<?= $u['id'] ?>" method="post" onsubmit="return confirm('Reset password ke 123123?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-info" title="Reset"><i class="bi bi-key"></i></button></form>
                            <form action="/sys/users/delete/<?= $u['id'] ?>" method="post" onsubmit="return confirm('Hapus user ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button></form>
                        </div>
                        <?php else: ?>
                        <span class="badge bg-secondary" style="font-size:0.65rem">Protected</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
