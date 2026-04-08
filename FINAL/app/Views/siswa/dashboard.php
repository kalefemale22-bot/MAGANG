<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Helpers\JadwalHelper; $jadwalGrouped = JadwalHelper::group($jadwalHariIni); ?>

<div class="page-header">
    <div>
        <h1>Dashboard</h1>
        <p>Selamat datang, <?= esc($siswa['nama']) ?></p>
    </div>
</div>

<div class="row g-2 mb-3">
    <div class="col-6 col-lg-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(99,102,241,0.12);color:#6366f1"><i class="bi bi-person-fill"></i></div>
            <div class="stat-body">
                <div class="stat-value" style="font-size:1rem"><?= esc($siswa['nama']) ?></div>
                <div class="stat-label"><?= esc($siswa['nama_kelas'] ?? '-') ?></div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(16,185,129,0.12);color:#10b981"><i class="bi bi-check-circle"></i></div>
            <div class="stat-body">
                <div class="stat-value"><?= $totalAbsen > 0 ? round(($totalHadir / $totalAbsen) * 100) : 0 ?>%</div>
                <div class="stat-label">Kehadiran</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card">
            <div class="stat-icon" style="background:rgba(245,158,11,0.12);color:#f59e0b"><i class="bi bi-calendar3"></i></div>
            <div class="stat-body">
                <div class="stat-value" style="font-size:1rem"><?= esc($semester['nama_semester'] ?? '-') ?></div>
                <div class="stat-label">Semester</div>
            </div>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header"><i class="bi bi-calendar-day me-1"></i>Jadwal Hari Ini</div>
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom table-hover mb-0">
                <thead><tr><th>Jam</th><th>Mata Pelajaran</th><th>Guru</th></tr></thead>
                <tbody>
                    <?php foreach ($jadwalGrouped as $j): ?>
                    <tr>
                        <td data-label="Jam"><span class="badge bg-primary"><?= JadwalHelper::jamLabel($j) ?></span><br><small class="text-muted"><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></small></td>
                        <td data-label="Mapel" class="fw-semibold"><?= esc($j['mapel_nama']) ?></td>
                        <td data-label="Guru"><?= esc($j['guru_nama']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($jadwalGrouped)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-4"><i class="bi bi-emoji-smile" style="font-size:1.5rem"></i><br>Tidak ada jadwal hari ini</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
