<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Input Absensi</h1><p>Pilih kelas</p></div>
</div>

<div class="alert alert-info py-2 mb-3">
    <i class="bi bi-info-circle me-1"></i>Absensi otomatis buka 06:30, tutup 15:30. Siswa klik sekali untuk semua mapel.
</div>

<div class="card-custom">
    <div class="card-body">
        <?php if (!empty($kelasList)): ?>
        <div class="row g-2">
            <?php foreach ($kelasList as $k): ?>
            <div class="col-6 col-md-4">
                <a href="/guru/absensi/input/<?= $k['id'] ?>" class="text-decoration-none">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(99,102,241,0.12);color:#6366f1"><i class="bi bi-building"></i></div>
                        <div class="stat-body">
                            <div class="stat-value" style="font-size:1.1rem"><?= esc($k['nama_kelas']) ?></div>
                            <div class="stat-label">Klik untuk input</div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="bi bi-calendar-x"></i><h5>Belum Ada Kelas</h5><p>Hubungi admin.</p></div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
