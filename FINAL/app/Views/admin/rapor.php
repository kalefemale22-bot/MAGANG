<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold">Rapor Siswa</h5>
    <form class="d-flex gap-2">
        <select name="kelas_id" class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()">
            <option value="">Pilih Kelas</option>
            <?php foreach ($kelasList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= ($selectedKelas == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
</div>

<?php if (empty($siswa)): ?>
<div class="card-custom p-5 text-center">
    <i class="bi bi-file-earmark-text" style="font-size:3rem;color:#94a3b8"></i>
    <p class="text-muted mt-3">Pilih kelas untuk melihat daftar siswa dan rapor.</p>
</div>
<?php else: ?>
<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>No. Siswa</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($siswa as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><code><?= esc($s['username']) ?></code></td>
                    <td class="fw-semibold"><?= esc($s['nama']) ?></td>
                    <td><?= $s['jenis_kelamin'] ?></td>
                    <td>
                        <a href="/admin/rapor/view/<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary me-1">
                            <i class="bi bi-eye me-1"></i>Lihat Rapor
                        </a>
                        <a href="/admin/rapor/print/<?= $s['id'] ?>" class="btn btn-sm btn-outline-success" target="_blank">
                            <i class="bi bi-printer me-1"></i>Print
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
