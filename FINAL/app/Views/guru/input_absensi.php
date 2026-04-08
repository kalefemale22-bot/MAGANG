<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php use App\Helpers\JadwalHelper; $jadwalGrouped = JadwalHelper::group($jadwal, true); $hariMap = ['Monday' => 'Senin','Tuesday' => 'Selasa','Wednesday' => 'Rabu','Thursday' => 'Kamis','Friday' => 'Jumat','Saturday' => 'Sabtu','Sunday' => 'Minggu']; $hariIni = $hariMap[date('l', strtotime($tanggal))] ?? date('l', strtotime($tanggal)); ?>

<div class="page-header">
    <div>
        <h1>Input Absensi</h1>
        <p><?= $hariIni ?>, <?= date('d/m/Y', strtotime($tanggal)) ?></p>
    </div>
</div>

<!-- Session Control -->
<div class="card-custom mb-2">
    <div class="card-body py-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="get" class="d-flex align-items-center gap-2 flex-grow-1">
                <i class="bi bi-calendar3 text-muted"></i>
                <input type="date" name="tanggal" class="form-control form-control-sm" style="max-width:150px" value="<?= $tanggal ?>" onchange="this.form.submit()">
                <span class="badge bg-secondary"><?= date('d/m/Y', strtotime($tanggal)) ?></span>
            </form>
            <?php if ($sessionStatus === 'open'): ?>
                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Sesi Terbuka</span>
                <?php if (!empty($session['expires_at'])): ?>
                <small class="text-muted">s/d <?= date('H:i', strtotime($session['expires_at'])) ?></small>
                <?php endif; ?>
                <form method="post" action="/guru/absensi/close-session">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kelas_id" value="<?= $kelas['id'] ?>">
                    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tutup sesi dan tandai Alpha?')"><i class="bi bi-lock"></i></button>
                </form>
            <?php elseif ($sessionStatus === 'expired'): ?>
                <span class="badge bg-secondary"><i class="bi bi-clock"></i> Kadaluarsa</span>
                <form method="post" action="/guru/absensi/open-session">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kelas_id" value="<?= $kelas['id'] ?>">
                    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                    <input type="hidden" name="durasi" value="540">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-unlock"></i></button>
                </form>
            <?php else: ?>
                <span class="badge bg-secondary"><i class="bi bi-hourglass"></i> Belum Dibuka</span>
                <form method="post" action="/guru/absensi/open-session">
                    <?= csrf_field() ?>
                    <input type="hidden" name="kelas_id" value="<?= $kelas['id'] ?>">
                    <input type="hidden" name="tanggal" value="<?= $tanggal ?>">
                    <input type="hidden" name="durasi" value="540">
                    <button type="submit" class="btn btn-sm btn-success"><i class="bi bi-unlock"></i> Buka Sesi</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (empty($jadwalList)): ?>
<div class="empty-state">
    <i class="bi bi-calendar-x"></i>
    <h5>Tidak Ada Jadwal</h5>
    <p>Tidak ada jadwal untuk kelas <?= esc($kelas['nama_kelas']) ?> hari <?= $hariIni ?>.</p>
</div>
<?php endif; ?>

<?php foreach ($jadwalList as $j): ?>
<?php $jamLabel = ($j['jam_awal'] == $j['jam_akhir']) ? "Jam ke-{$j['jam_awal']}" : "Jam ke-{$j['jam_awal']}-{$j['jam_akhir']}"; ?>
<div class="card-custom mb-2">
    <div class="card-header py-2">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
            <span class="fw-bold"><i class="bi bi-clock me-1"></i><?= $jamLabel ?> — <?= esc($j['mapel_nama']) ?></span>
            <span class="badge bg-light text-dark border"><i class="bi bi-people"></i> <?= count($siswa) ?></span>
        </div>
        <small class="text-muted"><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></small>
    </div>
    <div class="card-body p-0">
        <form method="post" action="/guru/absensi/store" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <?php foreach ($j['jadwal_ids'] as $jid): ?>
            <input type="hidden" name="jadwal_ids[]" value="<?= $jid ?>">
            <?php endforeach; ?>
            <input type="hidden" name="kelas_id" value="<?= $kelas['id'] ?>">
            <input type="hidden" name="tanggal" value="<?= $tanggal ?>">

            <div class="table-scroll">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr><th>#</th><th>Nama</th><th>Status</th><th>Keterangan</th><th>Foto Surat</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $i => $s): ?>
                        <?php $existing = $existingAbsensi[$j['id']][$s['id']] ?? null; $currentStatus = $existing['status'] ?? 'Alpha'; $needsFoto = in_array($currentStatus, ['Sakit','Izin']); ?>
                        <tr>
                            <td data-label="#"><?= $i + 1 ?></td>
                            <td data-label="Nama" class="fw-semibold"><?= esc($s['nama']) ?></td>
                            <td data-label="Status">
                                <select name="status[<?= $s['id'] ?>]" class="form-select form-select-sm status-select" data-siswa="<?= $s['id'] ?>">
                                    <?php foreach (['Hadir', 'Sakit', 'Izin', 'Alpha'] as $st): ?>
                                    <option value="<?= $st ?>" <?= ($existing && $existing['status'] === $st) || (!$existing && $st === 'Alpha') ? 'selected' : '' ?>><?= $st ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td data-label="Ket"><input type="text" name="keterangan[<?= $s['id'] ?>]" class="form-control form-control-sm" placeholder="Opsional" value="<?= esc($existing['keterangan'] ?? '') ?>"></td>
                            <td data-label="Foto" id="foto-cell-<?= $s['id'] ?>">
                                <div class="foto-upload-wrapper" style="display:<?= $needsFoto ? 'block' : 'none' ?>">
                                    <?php if (!empty($existing['foto_surat'])): ?>
                                    <a href="/uploads/surat_absensi/<?= esc($existing['foto_surat']) ?>" target="_blank" class="btn btn-outline-success btn-sm mb-1"><i class="bi bi-image"></i></a>
                                    <?php endif; ?>
                                    <input type="file" name="foto_surat[<?= $s['id'] ?>]" class="form-control form-control-sm foto-surat-input" accept="image/*,application/pdf" data-siswa="<?= $s['id'] ?>">
                                </div>
                                <span class="no-foto-msg text-muted" style="font-size:0.75rem;display:<?= $needsFoto ? 'none' : 'block' ?>">—</span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-2 text-end">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i>Simpan</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.status-select').forEach(function(sel) {
    sel.addEventListener('change', function() {
        const id = this.dataset.siswa;
        const cell = document.getElementById('foto-cell-' + id);
        const wrap = cell.querySelector('.foto-upload-wrapper');
        const noFoto = cell.querySelector('.no-foto-msg');
        const inp = cell.querySelector('.foto-surat-input');
        if (this.value === 'Sakit' || this.value === 'Izin') {
            wrap.style.display = 'block'; noFoto.style.display = 'none';
            if (inp) inp.required = true;
        } else {
            wrap.style.display = 'none'; noFoto.style.display = 'block';
            if (inp) { inp.required = false; inp.value = ''; }
        }
    });
});
</script>
<?= $this->endSection() ?>
