<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $hariMap=['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu']; $hariIni=$hariMap[date('l')] ?? date('l'); ?>

<div class="page-header">
    <div>
        <h1>Isi Absensi</h1>
        <p><?= $hariIni ?>, <?= date('d/m/Y') ?> <?= !empty($siswa['nama_kelas']) ? '| <span class="badge bg-primary">'.esc($siswa['nama_kelas']).'</span>' : '' ?></p>
    </div>
</div>

<?php if (!empty($jadwalList)): ?>
<div class="card-custom mb-3">
    <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center">
            <span><i class="bi bi-clipboard-check me-1"></i>Absensi Hari Ini</span>
            <?php if ($sudah_absen): ?>
            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Selesai</span>
            <?php elseif ($is_open): ?>
            <span class="badge bg-warning text-dark countdown-badge" data-sisa="<?= $sisa_detik ?>"><i class="bi bi-stopwatch"></i> <span class="countdown-text"></span></span>
            <?php elseif ($expired): ?>
            <span class="badge bg-danger"><i class="bi bi-x-circle"></i> Ditutup</span>
            <?php else: ?>
            <span class="badge bg-secondary"><i class="bi bi-hourglass"></i> Menunggu</span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <?php if ($sudah_absen): ?>
        <div class="alert alert-success mb-0">
            <i class="bi bi-check-circle-fill me-1"></i>Anda sudah mengisi absensi <?= count($jadwalList) ?> mapel hari ini.
            <?php if (!empty($siswa['is_monitoring']) && !empty($laporan)): ?>
            <br>Status Guru: <strong><?= ucfirst(str_replace('_', ' ', $laporan['status'] ?? '')) ?></strong>
            <?= !empty($laporan['keterangan']) ? ' — <em>'.esc($laporan['keterangan']).'</em>' : '' ?>
            <?php endif; ?>
        </div>
        <?php elseif ($is_open): ?>
        <?php if (!empty($siswa['is_monitoring'])): ?>
        <p class="text-muted mb-2" style="font-size:0.82rem"><i class="bi bi-info-circle me-1"></i>Klik Hadir untuk semua mapel + laporkan kehadiran guru.</p>
        <form method="post" action="/siswa/laporan-guru/store" class="mb-2">
            <?= csrf_field() ?>
            <div class="row g-2 mb-2">
                <div class="col-6">
                    <label class="form-label" style="font-size:0.78rem">Status Guru:</label>
                    <select name="status" class="form-select form-select-sm" required>
                        <option value="">Pilih</option>
                        <option value="hadir">Hadir</option>
                        <option value="tugas">Memberi Tugas</option>
                        <option value="tidak_hadir">Tidak Hadir</option>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label" style="font-size:0.78rem">Keterangan:</label>
                    <input type="text" name="keterangan" class="form-control form-control-sm" placeholder="Opsional">
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-send-check me-1"></i>Hadir & Laporkan</button>
        </form>
        <?php else: ?>
        <p class="text-muted mb-2" style="font-size:0.82rem"><i class="bi bi-info-circle me-1"></i>Klik untuk tercatat Hadir di <?= count($jadwalList) ?> mapel.</p>
        <form method="post" action="/siswa/laporan-guru/store">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-success w-100"><i class="bi bi-check-circle me-1"></i>Hadir Sekarang</button>
        </form>
        <?php endif; ?>
        <?php elseif ($expired): ?>
        <div class="alert alert-danger mb-0"><i class="bi bi-x-circle me-1"></i>Sesi ditutup. Hubungi guru.</div>
        <?php else: ?>
        <div class="alert alert-secondary mb-0"><i class="bi bi-hourglass me-1"></i>Absensi belum dibuka.</div>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<div class="card-custom">
    <div class="card-header py-2"><i class="bi bi-calendar-week me-1"></i>Daftar Mapel Hari Ini <span class="badge bg-primary ms-1"><?= count($jadwalList) ?></span></div>
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>Jam</th><th>Mata Pelajaran</th><th>Guru</th></tr></thead>
                <tbody>
                    <?php foreach ($jadwalList as $j): ?>
                    <tr>
                        <td data-label="Jam"><span class="badge bg-light text-dark border"><?= $j['jam_ke'] ?></span></td>
                        <td data-label="Mapel" class="fw-semibold"><?= esc($j['mapel_nama']) ?></td>
                        <td data-label="Guru" class="text-muted"><?= esc($j['guru_nama']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($jadwalList)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-4"><i class="bi bi-calendar-x" style="font-size:1.5rem"></i><br>Tidak ada jadwal.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.countdown-badge').forEach(badge => {
    let sisa = parseInt(badge.dataset.sisa);
    const textEl = badge.querySelector('.countdown-text');
    function update() {
        if (sisa <= 0) { textEl.textContent = 'Habis'; setTimeout(() => location.reload(), 1000); return; }
        const m = Math.floor(sisa / 60), s = sisa % 60;
        textEl.textContent = `${m}:${s.toString().padStart(2, '0')}`;
        sisa--;
        setTimeout(update, 1000);
    }
    update();
});
</script>
<?= $this->endSection() ?>
