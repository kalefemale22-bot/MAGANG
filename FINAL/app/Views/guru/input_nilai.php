<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div>
        <h1>Input Nilai</h1>
        <p><span class="badge bg-primary"><?= esc($mapel['nama']) ?> — <?= esc($kelas['nama_kelas']) ?></span></p>
    </div>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <form method="post" action="/guru/nilai/store">
            <?= csrf_field() ?>
            <input type="hidden" name="mapel_id" value="<?= $mapel['id'] ?>">
            <input type="hidden" name="kelas_id" value="<?= $kelas['id'] ?>">
            <div class="table-scroll">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr><th>#</th><th>Nama Siswa</th><?php foreach ($jenisNilai as $jn): ?><th class="text-center"><?= $jn ?></th><?php endforeach; ?></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $i => $s): ?>
                        <tr>
                            <td data-label="#"><?= $i + 1 ?></td>
                            <td data-label="Nama" class="fw-semibold">
                                <?= esc($s['nama']) ?>
                                <?php if (!empty($lockedStudents[$s['id']])): ?><i class="bi bi-lock-fill text-danger ms-1" title="Dikunci Wali"></i><?php endif; ?>
                            </td>
                            <?php foreach ($jenisNilai as $jn): ?>
                            <?php $isLocked = !empty($lockedStudents[$s['id']]); ?>
                            <td data-label="<?= $jn ?>">
                                <input type="number" name="nilai[<?= $s['id'] ?>][<?= $jn ?>]"
                                    class="form-control form-control-sm text-center"
                                    value="<?= $existingNilai[$s['id']][$jn] ?? '' ?>"
                                    min="0" max="100" step="0.01"
                                    style="width:70px"
                                    <?= $isLocked ? 'readonly style="width:70px;background:#f1f5f9"' : '' ?>>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-2 text-end">
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan Nilai</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
