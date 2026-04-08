<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Rekap Absensi</h1><p>Kelas <?= esc($kelasWali['nama_kelas']) ?></p></div>
</div>

<div class="card-custom mb-2">
    <div class="card-body">
        <form action="/guru/rekap-absensi" method="get" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label">Bulan</label>
                <select name="bulan" class="form-select">
                    <option value="all" <?= $selectedBulan == 'all' ? 'selected' : '' ?>>Semua</option>
                    <?php $months=['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'Mei','6'=>'Jun','7'=>'Jul','8'=>'Agu','9'=>'Sep','10'=>'Okt','11'=>'Nov','12'=>'Des']; ?>
                    <?php foreach ($months as $num => $name): ?>
                    <option value="<?= $num ?>" <?= $selectedBulan == $num ? 'selected' : '' ?>><?= $name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label">Tahun</label>
                <select name="tahun" class="form-select">
                    <?php $cy=date('Y'); for($y=$cy-2;$y<=$cy+1;$y++): ?>
                    <option value="<?= $y ?>" <?= $selectedTahun==$y?'selected':'' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-filter me-1"></i>Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header d-flex justify-content-between py-2">
        <span class="fw-bold"><i class="bi bi-table me-1"></i>Kehadiran</span>
        <button class="btn btn-sm btn-outline-success" onclick="window.print()"><i class="bi bi-printer"></i></button>
    </div>
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>#</th><th>NISN</th><th>Nama</th><th class="text-center text-success">H</th><th class="text-center text-warning">S</th><th class="text-center text-info">I</th><th class="text-center text-danger">A</th></tr></thead>
                <tbody>
                    <?php if (empty($rekapData)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4"><i class="bi bi-people" style="font-size:1.5rem"></i><br>Belum ada data.</td></tr>
                    <?php else: foreach ($rekapData as $i => $row): ?>
                    <tr>
                        <td data-label="#"><?= $i + 1 ?></td>
                        <td data-label="NISN"><code><?= esc($row['Siswa']['username']) ?></code></td>
                        <td data-label="Nama" class="fw-semibold"><?= esc($row['Siswa']['nama']) ?></td>
                        <td data-label="H" class="text-center fw-bold text-success"><?= $row['Hadir'] ?></td>
                        <td data-label="S" class="text-center fw-bold text-warning"><?= $row['Sakit'] ?></td>
                        <td data-label="I" class="text-center fw-bold text-info"><?= $row['Izin'] ?></td>
                        <td data-label="A" class="text-center fw-bold text-danger"><?= $row['Alpha'] ?></td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<style>
@media print { body { background:#fff; } .sidebar,.top-navbar,.bottom-nav,.card-custom:first-child,.btn,.page-header { display:none !important; } .main-content { margin:0 !important; padding:0 !important; } }
</style>

<?= $this->endSection() ?>
