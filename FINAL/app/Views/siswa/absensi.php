<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Rekap Absensi</h1></div>
</div>

<div class="row g-2 mb-2">
    <?php $rekapMap=[]; foreach($rekap as $r) $rekapMap[$r['status']]=$r['total']; ?>
    <?php $colors=['Hadir'=>'success','Sakit'=>'info','Izin'=>'warning','Alpha'=>'danger']; ?>
    <?php foreach (['Hadir','Sakit','Izin','Alpha'] as $st): ?>
    <div class="col-6 col-md-3">
        <div class="stat-card">
            <div class="stat-label"><?= $st ?></div>
            <div class="stat-value"><?= $rekapMap[$st] ?? 0 ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>Tanggal</th><th>Mapel</th><th>Status</th><th>Keterangan</th></tr></thead>
                <tbody>
                    <?php foreach ($absensi as $a): ?>
                    <tr>
                        <td data-label="Tanggal"><?= date('d/m/Y', strtotime($a['tanggal'])) ?></td>
                        <td data-label="Mapel" class="fw-semibold"><?= esc($a['mapel_nama']) ?></td>
                        <td data-label="Status"><span class="badge bg-<?= $colors[$a['status']] ?? 'secondary' ?>"><?= $a['status'] ?></span></td>
                        <td data-label="Ket"><?= esc($a['keterangan'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($absensi)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-clipboard-x" style="font-size:1.5rem"></i><br>Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
