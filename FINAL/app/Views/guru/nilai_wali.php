<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1">Nilai Siswa — Kelas <?= esc($kelasWali['nama_kelas']) ?></h5>
        <p class="text-muted mb-0" style="font-size:0.875rem">Semester <?= esc($semester['nama_semester'] ?? '-') ?></p>
    </div>
    <button class="btn btn-outline-secondary btn-sm no-print" onclick="window.print()">
        <i class="bi bi-printer me-1"></i>Cetak Nilai
    </button>
</div>

<?php if (empty($siswaList)): ?>
<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Tidak ada siswa di kelas ini.</div>
<?php else: ?>

<?php if (empty($mapelList)): ?>
<div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Belum ada data nilai untuk semester ini.</div>
<?php else: ?>

<!-- Filter Mapel -->
<div class="card-custom mb-3 no-print">
    <div class="card-body py-2 px-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-semibold" style="font-size:0.85rem">Filter Tampil:</span>
            <button class="btn btn-sm btn-primary filter-btn active" data-filter="semua">Semua Mapel (Ringkasan)</button>
            <button class="btn btn-sm btn-outline-secondary filter-btn" data-filter="detail">Detail Per Mapel</button>
        </div>
    </div>
</div>

<!-- VIEW: RINGKASAN (Nilai Akhir per Mapel) -->
<div id="view-semua">
    <div class="card-custom">
        <div class="card-header">
            <i class="bi bi-bar-chart me-2"></i>Rekap Nilai Akhir Semua Siswa
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0" style="font-size:0.82rem;">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>NISN</th>
                        <th style="min-width:180px">Nama Siswa</th>
                        <?php foreach ($mapelList as $mapel): ?>
                        <th class="text-center" style="min-width:80px"><?= esc($mapel['kode'] ?: $mapel['nama']) ?></th>
                        <?php endforeach; ?>
                        <th class="text-center" style="min-width:70px">Rata-rata</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswaList as $i => $s): ?>
                    <?php
                        $nilaiAkhirArr = [];
                        foreach ($mapelList as $mapel) {
                            $na = $nilaiData[$s['id']][$mapel['id']]['nilai_akhir'] ?? null;
                            $nilaiAkhirArr[] = $na;
                        }
                        $naFilled = array_filter($nilaiAkhirArr, fn($v) => $v !== null);
                        $rataRata = count($naFilled) > 0 ? round(array_sum($naFilled) / count($naFilled), 1) : null;
                    ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($s['username']) ?></td>
                        <td class="fw-semibold"><?= esc($s['nama']) ?></td>
                        <?php foreach ($mapelList as $mapel): ?>
                        <?php 
                            $na = $nilaiData[$s['id']][$mapel['id']]['nilai_akhir'] ?? null;
                            $predikat = $nilaiData[$s['id']][$mapel['id']]['predikat'] ?? null;
                        ?>
                        <td class="text-center">
                            <?php if ($na !== null): ?>
                                <span class="fw-bold"><?= number_format($na, 0) ?></span>
                                <?php if ($predikat): ?><small>(<?= $predikat ?>)</small><?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                        <td class="text-center fw-bold">
                            <?= $rataRata !== null ? $rataRata : '<span class="text-muted">—</span>' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Legend kode mapel -->
    <div class="card-custom mt-3">
        <div class="card-body py-2">
            <small class="text-muted fw-semibold">Keterangan Kode Mapel:</small>
            <div class="d-flex flex-wrap gap-2 mt-1">
                <?php foreach ($mapelList as $m): ?>
                <span class="badge" style="background:#f8f9fa;color:#333;border:1px solid #dee2e6;font-size:0.75rem"><?= esc($m['kode'] ?: '-') ?> = <?= esc($m['nama']) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- VIEW: DETAIL per mapel -->
<div id="view-detail" style="display:none">
    <?php foreach ($mapelList as $mapel): ?>
    <div class="card-custom mb-4">
        <div class="card-header">
            <i class="bi bi-book me-2"></i><?= esc($mapel['nama']) ?>
            <small class="text-muted ms-2">(<?= esc($mapel['kode']) ?>)</small>
        </div>
        <div class="table-responsive">
            <table class="table table-custom mb-0" style="font-size:0.82rem;">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NISN</th>
                        <th>Nama Siswa</th>
                        <?php foreach ($jenisNilai as $jn): ?>
                        <th class="text-center"><?= $jn ?></th>
                        <?php endforeach; ?>
                        <th class="text-center">Rata UH</th>
                        <th class="text-center fw-bold">N. Akhir</th>
                        <th class="text-center fw-bold">Predikat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($siswaList as $i => $s): ?>
                    <?php $nd = $nilaiData[$s['id']][$mapel['id']] ?? null; ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= esc($s['username']) ?></td>
                        <td class="fw-semibold"><?= esc($s['nama']) ?></td>
                        <?php foreach ($jenisNilai as $jn): ?>
                        <td class="text-center"><?= isset($nd['values'][$jn]) ? number_format($nd['values'][$jn], 0) : '<span class="text-muted">—</span>' ?></td>
                        <?php endforeach; ?>
                        <td class="text-center"><?= $nd['rata_uh'] ?? '<span class="text-muted">—</span>' ?></td>
                        <td class="text-center fw-bold"><?= $nd['nilai_akhir'] ?? '<span class="text-muted">—</span>' ?></td>
                        <td class="text-center fw-bold"><?= $nd['predikat'] ?? '<span class="text-muted">—</span>' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>
<?php endif; ?>

<style>
@media print {
    body { background: #fff !important; color: #000 !important; }
    .no-print, .sidebar, .navbar, .btn { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
    .card-custom { box-shadow: none !important; border: 1px solid #000 !important; margin-bottom: 10px !important; }
    .card-header { border-bottom: 2px solid #000 !important; background: #fff !important; color: #000 !important; }
    * { color: #000 !important; -webkit-print-color-adjust: exact; }
    table th, table td { border: 1px solid #000 !important; font-size: 8pt !important; }
    #view-detail { display: block !important; }
    @page { margin: 10mm; size: A4 landscape; }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('btn-primary', 'active');
            b.classList.add('btn-outline-secondary');
        });
        this.classList.add('btn-primary', 'active');
        this.classList.remove('btn-outline-secondary');

        const filter = this.dataset.filter;
        document.getElementById('view-semua').style.display = filter === 'semua' ? 'block' : 'none';
        document.getElementById('view-detail').style.display = filter === 'detail' ? 'block' : 'none';
    });
});
</script>
<?= $this->endSection() ?>
