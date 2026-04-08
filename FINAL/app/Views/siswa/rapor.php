<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Rapor Saya</h1>
        <p>Semester <?= esc($semester['nama_semester'] ?? '-') ?> — Kelas <?= esc($siswa['nama_kelas'] ?? '-') ?></p>
    </div>
</div>

<div class="card-custom">
    <div class="card-header"><i class="bi bi-journal-text me-1"></i>Nilai Akademik <span class="badge bg-primary"><?= count($nilaiPerMapel) ?> Mapel</span></div>
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>#</th><th>Mata Pelajaran</th><?php foreach ($jenisNilai as $jn): ?><th class="text-center"><?= $jn ?></th><?php endforeach; ?><th class="text-center">Akhir</th><th class="text-center">Predikat</th></tr></thead>
                <tbody>
                    <?php if (empty($nilaiPerMapel)): ?>
                    <tr><td colspan="<?= 3 + count($jenisNilai) ?>" class="text-center text-muted py-4"><i class="bi bi-journal-x" style="font-size:1.5rem"></i><br>Belum ada nilai.</td></tr>
                    <?php else: $no=1; foreach ($nilaiPerMapel as $mapel => $info): ?>
                    <tr>
                        <td data-label="#"><?= $no++ ?></td>
                        <td data-label="Mapel" class="fw-semibold"><?= esc($mapel) ?></td>
                        <?php foreach ($jenisNilai as $jn): ?>
                        <td data-label="<?= $jn ?>" class="text-center"><?= isset($info['values'][$jn]) ? number_format($info['values'][$jn], 0) : '-' ?></td>
                        <?php endforeach; ?>
                        <td data-label="Akhir" class="text-center fw-bold text-primary"><?= $info['nilai_akhir'] ?: '-' ?></td>
                        <td data-label="Predikat">
                            <?php $pc=$info['predikat']; $cls=$pc==='A'?'success':($pc==='B'?'primary':($pc==='C'?'warning':'danger')); ?>
                            <span class="badge bg-<?= $cls ?>"><?= $pc ?></span>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
