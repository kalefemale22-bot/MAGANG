<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Helpers\JadwalHelper; $jadwalGrouped = JadwalHelper::group($jadwal, true); ?>

<div class="page-header">
    <div><h1>Jadwal Pelajaran</h1><p>Kelas <?= esc($siswa['nama_kelas'] ?? '') ?></p></div>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom table-hover mb-0">
                <thead><tr><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Guru</th></tr></thead>
                <tbody>
                    <?php $lastHari=''; foreach ($jadwalGrouped as $j): ?>
                    <tr>
                        <td data-label="Hari" class="fw-semibold"><?= $j['hari'] !== $lastHari ? esc($j['hari']) : '' ?></td>
                        <td data-label="Jam"><span class="badge bg-primary"><?= JadwalHelper::jamLabel($j) ?></span><br><small class="text-muted"><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></small></td>
                        <td data-label="Mapel" class="fw-semibold"><?= esc($j['mapel_nama']) ?></td>
                        <td data-label="Guru"><?= esc($j['guru_nama']) ?></td>
                    </tr>
                    <?php $lastHari = $j['hari']; endforeach; ?>
                    <?php if (empty($jadwalGrouped)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-calendar-x" style="font-size:1.5rem"></i><br>Belum ada jadwal.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
