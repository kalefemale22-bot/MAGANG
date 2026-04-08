<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Nilai</h1><p><span class="badge bg-primary"><?= esc($siswa['nama']) ?></span></p></div>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>Mata Pelajaran</th><?php foreach ($jenisNilai as $jn): ?><th class="text-center"><?= $jn ?></th><?php endforeach; ?></tr></thead>
                <tbody>
                    <?php foreach ($nilaiPerMapel as $mapel => $scores): ?>
                    <tr>
                        <td data-label="Mapel" class="fw-semibold"><?= esc($mapel) ?></td>
                        <?php foreach ($jenisNilai as $jn): ?>
                        <td data-label="<?= $jn ?>" class="text-center"><?= isset($scores[$jn]) ? number_format($scores[$jn], 1) : '-' ?></td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($nilaiPerMapel)): ?>
                    <tr><td colspan="<?= count($jenisNilai) + 1 ?>" class="text-center text-muted py-4"><i class="bi bi-journal-x" style="font-size:1.5rem"></i><br>Belum ada nilai.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
