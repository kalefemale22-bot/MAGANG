<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Pengaturan Akademik</h1></div>
</div>

<!-- Tahun Ajaran -->
<div class="card-custom mb-3">
    <div class="card-header">
        <span><i class="bi bi-calendar-check me-1"></i>Tahun Ajaran</span>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTAModal">
            <i class="bi bi-plus"></i>
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead>
                    <tr><th>Tahun Ajaran</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($tahun_ajaran as $t): ?>
                    <tr>
                        <td data-label="Tahun" class="fw-semibold"><?= esc($t['nama']) ?></td>
                        <td data-label="Status">
                            <span class="badge bg-<?= $t['is_aktif'] ? 'success' : 'secondary' ?>"><?= $t['is_aktif'] ? 'Aktif' : 'Nonaktif' ?></span>
                        </td>
                        <td data-label="Aksi">
                            <div class="d-flex gap-1">
                                <?php if (!$t['is_aktif']): ?>
                                <form action="/admin/akademik/tahun-ajaran/activate/<?= $t['id'] ?>" method="post" onsubmit="return confirm('Aktifkan?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-success"><i class="bi bi-check-circle"></i></button>
                                </form>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editTA<?= $t['id'] ?>"><i class="bi bi-pencil"></i></button>
                                <?php if (!$t['is_aktif']): ?>
                                <form action="/admin/akademik/tahun-ajaran/delete/<?= $t['id'] ?>" method="post" onsubmit="return confirm('Hapus?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($tahun_ajaran)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Semester -->
<div class="card-custom">
    <div class="card-header">
        <span><i class="bi bi-window me-1"></i>Semester</span>
        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSemesterModal">
            <i class="bi bi-plus"></i>
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead>
                    <tr><th>Tahun / Semester</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($semester as $s): ?>
                    <tr>
                        <td data-label="Tahun" class="fw-semibold"><?= esc($s['nama_tahun']) ?> - <?= esc($s['nama_semester']) ?></td>
                        <td data-label="Status">
                            <span class="badge bg-<?= $s['is_aktif'] ? 'success' : 'secondary' ?>"><?= $s['is_aktif'] ? 'Berjalan' : 'Tutup' ?></span>
                        </td>
                        <td data-label="Aksi">
                            <?php if (!$s['is_aktif']): ?>
                            <div class="d-flex gap-1">
                                <form action="/admin/akademik/semester/activate/<?= $s['id'] ?>" method="post" onsubmit="return confirm('Jadikan aktif?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-success"><i class="bi bi-check-circle"></i></button>
                                </form>
                                <form action="/admin/akademik/semester/delete/<?= $s['id'] ?>" method="post" onsubmit="return confirm('Hapus?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                            <?php else: ?>
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Berjalan</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($semester)): ?>
                    <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Tahun Ajaran -->
<div class="modal fade" id="addTAModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/akademik/tahun-ajaran/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-1"></i>Tambah Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama Tahun Ajaran</label>
                    <input type="text" class="form-control" name="nama" placeholder="2025/2026" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Semester -->
<div class="modal fade" id="addSemesterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/akademik/semester/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-1"></i>Buka Semester Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="tahun_ajaran_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            <?php foreach($tahun_ajaran as $ta): ?>
                            <option value="<?= $ta['id'] ?>"><?= esc($ta['nama']) ?> <?= $ta['is_aktif'] ? '(Aktif)' : '' ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Tipe Semester</label>
                        <select name="nama_semester" class="form-select" required>
                            <option value="Ganjil">Ganjil</option><option value="Genap">Genap</option>
                        </select>
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

<!-- Edit modals for TA -->
<?php foreach ($tahun_ajaran as $t): ?>
<div class="modal fade" id="editTA<?= $t['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/akademik/tahun-ajaran/update/<?= $t['id'] ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title">Edit Tahun Ajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control" name="nama" value="<?= esc($t['nama']) ?>" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?= $this->endSection() ?>
