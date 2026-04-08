<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="mb-1 fw-bold">Rapor Siswa</h5>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size:0.85rem">
                <li class="breadcrumb-item"><a href="/admin/rapor">Pilih Kelas</a></li>
                <li class="breadcrumb-item active"><?= esc($siswa['nama']) ?></li>
            </ol>
        </nav>
    </div>
    <a href="/admin/rapor/print/<?= $siswa['id'] ?>" class="btn btn-success" target="_blank">
        <i class="bi bi-printer me-1"></i>Print Rapor
    </a>
</div>

<!-- Info Siswa -->
<div class="card-custom mb-4">
    <div class="card-header d-flex justify-content-between">
        <span><i class="bi bi-person-badge me-2"></i>Identitas Siswa</span>
        <span class="badge bg-purple"><?= esc($semester['nama_semester'] ?? '') ?></span>
    </div>
    <div class="card-body p-3">
        <div class="row">
            <div class="col-md-3"><small class="text-muted">Nama</small><div class="fw-bold"><?= esc($siswa['nama']) ?></div></div>
            <div class="col-md-3"><small class="text-muted">No. Siswa</small><div class="fw-bold"><?= esc($siswa['username']) ?></div></div>
            <div class="col-md-3"><small class="text-muted">Kelas</small><div class="fw-bold"><?= esc($siswa['nama_kelas'] ?? '-') ?></div></div>
            <div class="col-md-3"><small class="text-muted">Wali Kelas</small><div class="fw-bold"><?= esc($waliKelas['nama'] ?? '-') ?></div></div>
        </div>
    </div>
</div>

<!-- Tabel Nilai -->
<div class="card-custom">
    <div class="card-header"><i class="bi bi-journal-text me-2"></i>Nilai Akademik</div>
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mata Pelajaran</th>
                    <?php foreach ($jenisNilai as $jn): ?>
                    <th class="text-center"><?= $jn ?></th>
                    <?php endforeach; ?>
                    <th class="text-center">Rata UH</th>
                    <th class="text-center">N. Akhir</th>
                    <th class="text-center">Predikat</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($nilaiPerMapel)): ?>
                <tr><td colspan="<?= 4 + count($jenisNilai) ?>" class="text-center text-muted py-4">Belum ada data nilai</td></tr>
                <?php else: ?>
                <?php $no = 1; foreach ($nilaiPerMapel as $mapel => $info): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-semibold"><?= esc($mapel) ?></td>
                    <?php foreach ($jenisNilai as $jn): ?>
                    <td class="text-center"><?= isset($info['values'][$jn]) ? number_format($info['values'][$jn], 0) : '-' ?></td>
                    <?php endforeach; ?>
                    <td class="text-center fw-bold"><?= $info['rata_uh'] ?: '-' ?></td>
                    <td class="text-center fw-bold text-primary"><?= $info['nilai_akhir'] ?: '-' ?></td>
                    <td class="text-center">
                        <span class="badge bg-<?= $info['predikat'] === 'A' ? 'success' : ($info['predikat'] === 'B' ? 'primary' : ($info['predikat'] === 'C' ? 'warning' : 'danger')) ?>" style="font-size:0.85rem">
                            <?= $info['predikat'] ?>
                        </span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
