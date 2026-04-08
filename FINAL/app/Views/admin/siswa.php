<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show mb-3">
    <i class="bi bi-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show mb-3">
    <i class="bi bi-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">Data Siswa</h5>
    <div class="d-flex gap-2 flex-wrap">
        <a href="/admin/siswa/template" class="btn btn-outline-success btn-sm">
            <i class="bi bi-download me-1"></i>Template
        </a>
        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload me-1"></i>Import Excel
        </button>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addSiswaModal">
            <i class="bi bi-plus-lg me-1"></i>Tambah
        </button>
    </div>
</div>

<!-- Status Tabs -->
<ul class="nav nav-pills mb-3 gap-1 flex-wrap">
    <li class="nav-item">
        <a class="nav-link <?= $selectedStatus === 'aktif' ? 'active' : '' ?>" href="/admin/siswa?status=aktif">
            <i class="bi bi-check-circle me-1"></i>Aktif <span class="badge bg-white text-dark ms-1"><?= $statusCounts['aktif'] ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $selectedStatus === 'lulus' ? 'active' : '' ?>" href="/admin/siswa?status=lulus" style="<?= $selectedStatus !== 'lulus' ? 'color:#64748b' : '' ?>">
            <i class="bi bi-mortarboard me-1"></i>Lulus <span class="badge bg-secondary ms-1"><?= $statusCounts['lulus'] ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $selectedStatus === 'pindah' ? 'active' : '' ?>" href="/admin/siswa?status=pindah" style="<?= $selectedStatus !== 'pindah' ? 'color:#64748b' : '' ?>">
            <i class="bi bi-arrow-left-right me-1"></i>Pindah <span class="badge bg-secondary ms-1"><?= $statusCounts['pindah'] ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $selectedStatus === 'keluar' ? 'active' : '' ?>" href="/admin/siswa?status=keluar" style="<?= $selectedStatus !== 'keluar' ? 'color:#64748b' : '' ?>">
            <i class="bi bi-x-circle me-1"></i>Keluar <span class="badge bg-secondary ms-1"><?= $statusCounts['keluar'] ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $selectedStatus === 'semua' ? 'active' : '' ?>" href="/admin/siswa?status=semua" style="<?= $selectedStatus !== 'semua' ? 'color:#64748b' : '' ?>">
            <i class="bi bi-list me-1"></i>Semua
        </a>
    </li>
</ul>

<!-- Filter Kelas -->
<div class="d-flex align-items-center gap-2 mb-3">
    <form class="d-flex gap-2 align-items-center">
        <input type="hidden" name="status" value="<?= $selectedStatus ?>">
        <label class="fw-semibold text-muted" style="font-size:0.85rem;white-space:nowrap">Filter Kelas:</label>
        <select name="kelas_id" class="form-select form-select-sm" style="width:160px" onchange="this.form.submit()">
            <option value="">Semua Kelas</option>
            <?php foreach ($kelasList as $k): ?>
            <option value="<?= $k['id'] ?>" <?= ($selectedKelas == $k['id']) ? 'selected' : '' ?>><?= esc($k['nama_kelas']) ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <span class="badge bg-purple"><?= count($siswa) ?> siswa</span>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NISN</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Kelas</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($siswa)): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-people" style="font-size:2rem"></i><br>Tidak ada data siswa dengan status ini.
                </td></tr>
                <?php else: ?>
                <?php foreach ($siswa as $i => $s): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><code><?= esc($s['username']) ?></code></td>
                    <td class="fw-semibold"><?= esc($s['nama']) ?></td>
                    <td><span class="badge <?= $s['jenis_kelamin'] === 'L' ? 'bg-info' : 'bg-pink' ?>" style="<?= $s['jenis_kelamin'] === 'P' ? 'background:#ec4899!important' : '' ?>"><?= $s['jenis_kelamin'] ?></span></td>
                    <td><?= esc($s['nama_kelas'] ?? '-') ?></td>
                    <td>
                        <?php
                        $statusBg = match($s['status']) {
                            'aktif' => 'success',
                            'lulus' => 'primary',
                            'pindah' => 'warning text-dark',
                            'keluar' => 'danger',
                            default => 'secondary'
                        };
                        ?>
                        <span class="badge bg-<?= $statusBg ?>"><?= ucfirst($s['status']) ?></span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick='editSiswa(<?= json_encode($s) ?>)'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="/admin/siswa/delete/<?= $s['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus siswa ini?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/siswa/import" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-upload me-2"></i>Import Siswa dari Excel</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2" style="font-size:0.85rem">
                        <i class="bi bi-info-circle me-1"></i>
                        Download <a href="/admin/siswa/template" class="fw-bold">template Excel</a> terlebih dahulu, isi data, lalu upload.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Excel (.xlsx)</label>
                        <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="text-muted" style="font-size:0.8rem">
                        <p class="mb-1"><strong>Format kolom:</strong></p>
                        <code>A: No Siswa | B: Nama | C: JK (L/P) | D: NISN | E: Kelas</code>
                        <p class="mt-2 mb-0">Password default: <code>123123</code>. Username yang sudah ada akan dilewati.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-upload me-1"></i>Import Sekarang</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Siswa -->
<div class="modal fade" id="addSiswaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="/admin/siswa/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Siswa</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">NISN</label>
                            <input type="text" name="nisn" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">NIS</label>
                            <input type="text" name="nis" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select name="kelas_id" class="form-select">
                                <option value="">Belum ditempatkan</option>
                                <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Nama Orang Tua</label>
                            <input type="text" name="nama_ortu" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">No. HP Orang Tua</label>
                            <input type="text" name="no_hp_ortu" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tahun Masuk</label>
                            <input type="number" name="tahun_masuk" class="form-control" value="<?= date('Y') ?>">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Siswa -->
<div class="modal fade" id="editSiswaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editSiswaForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Siswa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nomor Siswa</label>
                            <input type="text" name="username" id="edit_siswa_username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" id="edit_siswa_nama" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_siswa_jk" class="form-select" required>
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">NISN</label>
                            <input type="text" name="nisn" id="edit_siswa_nisn" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">NIS</label>
                            <input type="text" name="nis" id="edit_siswa_nis" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <select name="kelas_id" id="edit_siswa_kelas" class="form-select">
                                <option value="">Belum ditempatkan</option>
                                <?php foreach ($kelasList as $k): ?>
                                <option value="<?= $k['id'] ?>"><?= esc($k['nama_kelas']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" id="edit_siswa_tempat" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" id="edit_siswa_tgl" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Nama Orang Tua</label>
                            <input type="text" name="nama_ortu" id="edit_siswa_ortu" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">No. HP Orang Tua</label>
                            <input type="text" name="no_hp_ortu" id="edit_siswa_hp" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="edit_siswa_status" class="form-select">
                                <option value="aktif">Aktif</option>
                                <option value="lulus">Lulus</option>
                                <option value="pindah">Pindah</option>
                                <option value="keluar">Keluar</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function editSiswa(s) {
    document.getElementById('editSiswaForm').action = '/admin/siswa/update/' + s.id;
    document.getElementById('edit_siswa_username').value = s.username || '';
    document.getElementById('edit_siswa_nama').value = s.nama || '';
    document.getElementById('edit_siswa_jk').value = s.jenis_kelamin || 'L';
    document.getElementById('edit_siswa_nisn').value = s.nisn || '';
    document.getElementById('edit_siswa_nis').value = s.nis || '';
    document.getElementById('edit_siswa_kelas').value = s.kelas_id || '';
    document.getElementById('edit_siswa_tempat').value = s.tempat_lahir || '';
    document.getElementById('edit_siswa_tgl').value = s.tanggal_lahir || '';
    document.getElementById('edit_siswa_ortu').value = s.nama_ortu || '';
    document.getElementById('edit_siswa_hp').value = s.no_hp_ortu || '';
    document.getElementById('edit_siswa_status').value = s.status || 'aktif';
    new bootstrap.Modal(document.getElementById('editSiswaModal')).show();
}
</script>
<?= $this->endSection() ?>
