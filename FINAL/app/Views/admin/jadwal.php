<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
use App\Helpers\JadwalHelper;
$jadwalGrouped = JadwalHelper::group($jadwalList, true);
?>

<!-- Add Jadwal Form -->
<div class="card-custom mb-3">
    <div class="card-header"><i class="bi bi-plus-circle me-2"></i>Tambah Jadwal</div>
    <div class="card-body">
        <form method="post" action="/admin/jadwal/store">
            <?= csrf_field() ?>
            <div class="row g-2">
                <div class="col-md-2">
                    <select name="kelas_id" class="form-select form-select-sm" required>
                        <option value="">Pilih Kelas</option>
                        <?php foreach ($kelasList as $k): ?>
                        <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="mapel_id" class="form-select form-select-sm" required>
                        <option value="">Pilih Mapel</option>
                        <?php foreach ($mapelList as $m): ?>
                        <option value="<?= $m['id'] ?>"><?= esc($m['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="guru_id" class="form-select form-select-sm" required>
                        <option value="">Pilih Guru</option>
                        <?php foreach ($guruList as $g): ?>
                        <option value="<?= $g['id'] ?>"><?= esc($g['nama']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <select name="hari" class="form-select form-select-sm" required>
                        <option value="">Hari</option>
                        <?php foreach (['Senin','Selasa','Rabu','Kamis','Jumat'] as $h): ?>
                        <option value="<?= $h ?>"><?= $h ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <input type="number" name="jam_ke" class="form-control form-control-sm" placeholder="Jam ke" min="1" max="10" required>
                </div>
                <div class="col-md-1">
                    <input type="time" name="jam_mulai" class="form-control form-control-sm" value="07:30" required>
                </div>
                <div class="col-md-1">
                    <input type="time" name="jam_selesai" class="form-control form-control-sm" value="08:15" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bi bi-plus me-1"></i>Tambah</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Filter -->
<div class="card-custom mb-3">
    <div class="card-body py-2 px-3">
        <form method="get" class="d-flex align-items-center gap-2">
            <label class="fw-semibold" style="font-size:0.85rem">Filter:</label>
            <select name="kelas_id" class="form-select form-select-sm" style="width:180px" onchange="this.form.submit()">
                <option value="">Semua Kelas</option>
                <?php foreach ($kelasList as $k): ?>
                <option value="<?= $k['id'] ?>" <?= $selectedKelas == $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
                <?php endforeach; ?>
            </select>
            <span class="badge bg-secondary"><?= count($jadwalGrouped) ?> blok jadwal</span>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header"><i class="bi bi-calendar3 me-2"></i>Jadwal Pelajaran</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-custom table-hover mb-0">
                <thead>
                    <tr>
                        <th>Hari</th>
                        <th>Jam Ke</th>
                        <th>Waktu</th>
                        <th>Kelas/Rombel</th>
                        <th>Mata Pelajaran</th>
                        <th>Guru</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $lastHari = ''; ?>
                    <?php foreach ($jadwalGrouped as $j): ?>
                    <tr>
                        <td class="fw-semibold"><?= $j['hari'] !== $lastHari ? esc($j['hari']) : '' ?></td>
                        <td><span class="badge bg-secondary"><?= JadwalHelper::jamLabel($j) ?></span></td>
                        <td><?= $j['jam_mulai'] ?> - <?= $j['jam_selesai'] ?></td>
                        <td><?= esc($j['nama_kelas'] ?? $j['nama_rombel'] ?? '-') ?></td>
                        <td><?= esc($j['mapel_nama']) ?></td>
                        <td><?= esc($j['guru_nama']) ?></td>
                    </tr>
                    <?php $lastHari = $j['hari']; ?>
                    <?php endforeach; ?>
                    <?php if (empty($jadwalGrouped)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Belum ada jadwal.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
