<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
$hariIni = $hariMap[date('l', strtotime($tanggal))] ?? date('l', strtotime($tanggal));
?>

<style>
    .bar-container { display:flex; height:28px; border-radius:6px; overflow:hidden; background:#f1f5f9; width:100%; min-width:120px; }
    .bar-segment { display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:600; color:#fff; transition: width 0.4s ease; min-width:0; }
    .bar-hadir { background: #10b981; }
    .bar-tugas { background: #f59e0b; }
    .bar-tidak { background: #ef4444; }
    .bar-belum { background: #cbd5e1; color: #64748b !important; }
    .entry-row:hover { background: #f8fafc; }
    .kesimpulan-hadir { background:rgba(16,185,129,0.1); color:#10b981; }
    .kesimpulan-tugas { background:rgba(245,158,11,0.1); color:#f59e0b; }
    .kesimpulan-tidak_hadir { background:rgba(239,68,68,0.1); color:#ef4444; }
    .legend-dot { width:10px; height:10px; border-radius:50%; display:inline-block; }
</style>

<!-- Date Navigation -->
<div class="card-custom mb-3">
    <div class="card-body py-2 px-3">
        <form method="get" class="d-flex align-items-center gap-2 flex-wrap">
            <label class="fw-semibold" style="font-size:0.85rem;white-space:nowrap">
                <i class="bi bi-calendar3 me-1"></i>Tanggal:
            </label>
            <input type="date" name="tanggal" class="form-control form-control-sm" style="width:160px" value="<?= $tanggal ?>" onchange="this.form.submit()">
            <span class="badge bg-secondary"><?= $hariIni ?>, <?= date('d/m/Y', strtotime($tanggal)) ?></span>

            <?php if (!empty($availDates)): ?>
            <div class="dropdown ms-auto">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-clock-history me-1"></i>Riwayat
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="max-height:300px;overflow-y:auto">
                    <?php foreach ($availDates as $d): ?>
                    <li>
                        <a class="dropdown-item d-flex justify-content-between gap-3 <?= $d['tanggal'] === $tanggal ? 'active' : '' ?>" href="?tanggal=<?= $d['tanggal'] ?>">
                            <span><?= date('d/m/Y', strtotime($d['tanggal'])) ?></span>
                            <span class="badge bg-secondary rounded-pill"><?= $d['jumlah'] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Stats Summary -->
<?php if ($stats['total_laporan'] > 0): ?>
<div class="row g-2 mb-3">
    <div class="col-4">
        <div class="stat-card" style="padding:0.75rem 1rem">
            <div class="stat-label" style="font-size:0.7rem">Kelas Dilaporkan</div>
            <div class="stat-value" style="font-size:1.3rem"><?= $stats['total_entries'] ?></div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card" style="padding:0.75rem 1rem">
            <div class="stat-label" style="font-size:0.7rem">Total Laporan</div>
            <div class="stat-value" style="font-size:1.3rem"><?= $stats['total_laporan'] ?></div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card" style="padding:0.75rem 1rem">
            <div class="stat-label" style="font-size:0.7rem">Terverifikasi</div>
            <div class="stat-value" style="font-size:1.3rem"><?= $stats['verified'] ?> <small style="font-size:0.6rem;color:#94a3b8">/ <?= $stats['total_laporan'] ?></small></div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Legend -->
<div class="d-flex gap-3 mb-2 flex-wrap" style="font-size:0.75rem">
    <span><span class="legend-dot" style="background:#10b981"></span> Hadir</span>
    <span><span class="legend-dot" style="background:#f59e0b"></span> Tugas</span>
    <span><span class="legend-dot" style="background:#ef4444"></span> Tidak Hadir</span>
    <span><span class="legend-dot" style="background:#cbd5e1"></span> Belum Jawab</span>
</div>

<!-- Per-Kelas Table -->
<div class="card-custom">
    <div class="card-header">
        <i class="bi bi-clipboard-check me-2"></i>Monitoring Per Kelas — <?= date('d/m/Y', strtotime($tanggal)) ?>
    </div>
    <div class="card-body p-0">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th style="width:25%">Mapel & Guru</th>
                    <th style="width:80px">Kelas</th>
                    <th>Distribusi Jawaban</th>
                    <th style="width:100px" class="text-center">Kesimpulan</th>
                    <th style="width:100px" class="text-center">Verifikasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($entries)): ?>
                <tr><td colspan="5" class="text-center text-muted py-4">
                    <i class="bi bi-inbox" style="font-size:2rem"></i><br>
                    Belum ada laporan pada tanggal ini.
                </td></tr>
                <?php else: ?>
                <?php foreach ($entries as $e): ?>
                <?php
                    $total = $e['hadir'] + $e['tugas'] + $e['tidak_hadir'] + $e['belum'];
                    $pctHadir = $total > 0 ? round($e['hadir'] / $total * 100) : 0;
                    $pctTugas = $total > 0 ? round($e['tugas'] / $total * 100) : 0;
                    $pctTidak = $total > 0 ? round($e['tidak_hadir'] / $total * 100) : 0;
                    $pctBelum = max(0, 100 - $pctHadir - $pctTugas - $pctTidak);
                ?>
                <tr class="entry-row">
                    <td>
                        <div class="fw-semibold" style="font-size:0.85rem"><?= esc($e['mapel_nama']) ?></div>
                        <small class="text-muted"><?= esc($e['guru_nama']) ?></small>
                    </td>
                    <td><span class="badge bg-light text-dark border"><?= esc($e['nama_kelas']) ?></span></td>
                    <td>
                        <div class="bar-container" title="Hadir: <?= $e['hadir'] ?> | Tugas: <?= $e['tugas'] ?> | Tidak Hadir: <?= $e['tidak_hadir'] ?> | Belum: <?= $e['belum'] ?>">
                            <?php if ($e['hadir'] > 0): ?><div class="bar-segment bar-hadir" style="width:<?= $pctHadir ?>%"><?= $e['hadir'] ?></div><?php endif; ?>
                            <?php if ($e['tugas'] > 0): ?><div class="bar-segment bar-tugas" style="width:<?= $pctTugas ?>%"><?= $e['tugas'] ?></div><?php endif; ?>
                            <?php if ($e['tidak_hadir'] > 0): ?><div class="bar-segment bar-tidak" style="width:<?= $pctTidak ?>%"><?= $e['tidak_hadir'] ?></div><?php endif; ?>
                            <?php if ($e['belum'] > 0): ?><div class="bar-segment bar-belum" style="width:<?= $pctBelum ?>%"><?= $e['belum'] ?></div><?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:0.7rem;color:#94a3b8">
                            <span><?= $e['total_jawab'] ?>/<?= $e['total_siswa'] ?> menjawab</span>
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge kesimpulan-<?= $e['kesimpulan'] ?>" style="font-size:0.75rem;padding:0.4em 0.8em;border-radius:6px">
                            <?= $e['kesimpulan'] === 'hadir' ? '✅ Hadir' : ($e['kesimpulan'] === 'tugas' ? '📋 Tugas' : '❌ Tidak Hadir') ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <?php if ($e['verified_count'] >= $e['total_jawab'] && $e['total_jawab'] > 0): ?>
                        <span class="badge bg-success"><i class="bi bi-check-lg"></i> Verified</span>
                        <?php else: ?>
                        <span class="text-muted" style="font-size:0.75rem"><?= $e['verified_count'] ?>/<?= $e['total_jawab'] ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
