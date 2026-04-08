<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold">Mata Pelajaran <span class="badge bg-purple ms-2"><?= count($mapelList) ?></span></h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMapelModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Mapel
    </button>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode</th>
                    <th>Nama Mata Pelajaran</th>
                    <th>Kelompok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($mapelList as $i => $m): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><code><?= esc($m['kode']) ?></code></td>
                    <td class="fw-semibold"><?= esc($m['nama']) ?></td>
                    <td><span class="badge bg-<?= str_contains($m['kelompok'] ?? '', 'Peminatan') ? 'warning' : 'info' ?>"><?= esc($m['kelompok'] ?? '-') ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick='editMapel(<?= json_encode($m) ?>)'>
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="/admin/mapel/delete/<?= $m['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus mapel ini?')">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Tambah Mapel -->
<div class="modal fade" id="addMapelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/mapel/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode</label>
                        <input type="text" name="kode" class="form-control" placeholder="contoh: MTK" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelompok</label>
                        <select name="kelompok" class="form-select">
                            <option value="Wajib A">Wajib A</option>
                            <option value="Wajib B">Wajib B</option>
                            <option value="Peminatan">Peminatan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
                            <option value="BK">BK</option>
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

<!-- Modal Edit Mapel -->
<div class="modal fade" id="editMapelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editMapelForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Mata Pelajaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kode</label>
                        <input type="text" name="kode" id="edit_mapel_kode" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Mata Pelajaran</label>
                        <input type="text" name="nama" id="edit_mapel_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kelompok</label>
                        <select name="kelompok" id="edit_mapel_kelompok" class="form-select">
                            <option value="Wajib A">Wajib A</option>
                            <option value="Wajib B">Wajib B</option>
                            <option value="Peminatan">Peminatan</option>
                            <option value="Muatan Lokal">Muatan Lokal</option>
                            <option value="BK">BK</option>
                        </select>
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
function editMapel(m) {
    document.getElementById('editMapelForm').action = '/admin/mapel/update/' + m.id;
    document.getElementById('edit_mapel_kode').value = m.kode;
    document.getElementById('edit_mapel_nama').value = m.nama;
    document.getElementById('edit_mapel_kelompok').value = m.kelompok || '';
    new bootstrap.Modal(document.getElementById('editMapelModal')).show();
}
</script>
<?= $this->endSection() ?>
