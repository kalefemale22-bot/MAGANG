<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Kenaikan Kelas & Kelulusan</h1></div>
</div>

<div class="card-custom mb-3">
    <div class="card-body">
        <form action="" method="get" class="row g-2 align-items-end">
            <div class="col-12 col-md-5">
                <label class="form-label">Kelas Asal</label>
                <select class="form-select" name="kelas_asal_id" required onchange="this.form.submit()">
                    <option value="">-- Pilih --</option>
                    <?php foreach ($semua_kelas as $k): ?>
                    <option value="<?= $k['id'] ?>" <?= $kelas_asal_id == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_tahun']) ?> - <?= esc($k['nama_kelas']) ?> (<?= $k['tingkat'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($kelas_asal): ?>
            <div class="col-12"><div class="alert alert-success py-1 mb-0" style="font-size:0.8rem"><i class="bi bi-check-circle-fill me-1"></i><?= count($siswa) ?> Siswa ditemukan</div></div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?php if ($kelas_asal): ?>
<form action="/admin/akademik/kenaikan-kelas/process" method="post" id="formKenaikanKelas" onsubmit="return confirm('Data sudah benar? Aksi ini bersifat permanen.')">
    <?= csrf_field() ?>
    <input type="hidden" name="kelas_asal_id" value="<?= $kelas_asal['id'] ?>">

    <div class="card-custom mb-3">
        <div class="card-header"><i class="bi bi-layers-fill me-1"></i>Tentukan Tujuan</div>
        <div class="card-body">
            <div class="row g-2">
                <div class="col-12 col-md-6">
                    <label class="form-label">Pindah / Naik ke:</label>
                    <select class="form-select" name="kelas_tujuan_id" required>
                        <option value="">-- Tentukan --</option>
                        <optgroup label="Naik Kelas">
                            <?php foreach ($semua_kelas as $k): ?>
                            <?php if ($k['id'] != $kelas_asal['id']): ?>
                            <option value="<?= $k['id'] ?>"><?= esc($k['nama_tahun']) ?> - <?= esc($k['nama_kelas']) ?></option>
                            <?php endif; ?>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="Aksi Keluar">
                            <?php if ($kelas_asal['tingkat'] == 'XII'): ?>
                            <option value="lulus">Lulus Sekolah (Alumni)</option>
                            <?php endif; ?>
                            <option value="keluar">Keluar / Dikeluarkan</option>
                            <option value="pindah">Pindah Sekolah</option>
                        </optgroup>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Eksekusi</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card-custom">
        <div class="card-header"><i class="bi bi-people me-1"></i>Daftar Siswa</div>
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="table table-custom mb-0">
                    <thead>
                        <tr><th><input type="checkbox" id="checkAll" checked class="form-check-input"></th><th>NIS</th><th>Nama</th><th>Status</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($siswa as $s): ?>
                        <tr>
                            <td><input type="checkbox" name="siswa_ids[]" value="<?= $s['id'] ?>" checked class="form-check-input check-item"></td>
                            <td data-label="NIS"><code><?= esc($s['nis'] ?: $s['username']) ?></code></td>
                            <td data-label="Nama" class="fw-semibold"><?= esc($s['nama']) ?></td>
                            <td data-label="Status"><span class="badge bg-success">Aktif</span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($siswa)): ?>
                        <tr><td colspan="4" class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:1.5rem"></i><br>Kelas kosong.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</form>
<?php else: ?>
<div class="empty-state">
    <i class="bi bi-arrow-up-circle"></i>
    <h5>Kenaikan Kelas Massal</h5>
    <p>Pilih kelas asal untuk memproses kenaikan / kelulusan.</p>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.getElementById('checkAll')?.addEventListener('change', function() {
    document.querySelectorAll('.check-item').forEach(cb => cb.checked = this.checked);
});
</script>
<?= $this->endSection() ?>
