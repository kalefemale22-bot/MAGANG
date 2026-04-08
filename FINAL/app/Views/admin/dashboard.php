<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Total Guru</div>
                    <div class="stat-value"><?= $totalGuru ?></div>
                </div>
                <div class="stat-icon" style="background: rgba(99,102,241,0.1); color: #6366f1;">
                    <i class="bi bi-person-badge"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Total Siswa</div>
                    <div class="stat-value"><?= $totalSiswa ?></div>
                </div>
                <div class="stat-icon" style="background: rgba(14,165,233,0.1); color: #0ea5e9;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Total Kelas</div>
                    <div class="stat-value"><?= $totalKelas ?></div>
                </div>
                <div class="stat-icon" style="background: rgba(16,185,129,0.1); color: #10b981;">
                    <i class="bi bi-building"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="stat-label">Mata Pelajaran</div>
                    <div class="stat-value"><?= $totalMapel ?></div>
                </div>
                <div class="stat-icon" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                    <i class="bi bi-book"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Kelas Overview -->
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-building me-2"></i>Daftar Kelas & Wali Kelas</span>
                <span class="badge bg-primary"><?= count($kelasList) ?> kelas</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-custom table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Tingkat</th>
                            <th>Wali Kelas</th>
                            <th>Jurusan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kelasList as $k): ?>
                        <tr>
                            <td class="fw-semibold"><?= esc($k['nama_kelas']) ?></td>
                            <td><span class="badge bg-<?= $k['tingkat'] === 'X' ? 'success' : ($k['tingkat'] === 'XI' ? 'primary' : 'warning text-dark') ?>"><?= $k['tingkat'] ?></span></td>
                            <td><?= esc($k['wali_nama'] ?? '-') ?></td>
                            <td><?= esc($k['jurusan'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom">
            <div class="card-header">
                <i class="bi bi-clock-history me-2"></i>Login Terakhir
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($recentLogin as $login): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-3 py-2" style="font-size:0.85rem">
                        <div>
                            <span class="fw-semibold"><?= esc($login['username']) ?></span>
                            <span class="badge bg-secondary ms-1" style="font-size:0.65rem"><?= ucfirst($login['role']) ?></span>
                        </div>
                        <small class="text-muted"><?= $login['last_login'] ? date('d/m H:i', strtotime($login['last_login'])) : '-' ?></small>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($recentLogin)): ?>
                    <div class="list-group-item text-center text-muted py-3">Belum ada login</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
