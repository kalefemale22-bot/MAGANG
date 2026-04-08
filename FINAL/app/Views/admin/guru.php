<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold">Data Guru <span class="badge bg-purple ms-2"><?= count($guru) ?></span></h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuruModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Guru
    </button>
</div>

<div class="card-custom">
    <div class="table-responsive">
        <table class="table table-custom mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>NUPTK</th>
                    <th>Nama</th>
                    <th>JK</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($guru as $i => $g): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td><code><?= esc($g['nuptk']) ?></code></td>
                    <td class="fw-semibold"><?= esc($g['nama']) ?></td>
                    <td><span class="badge <?= $g['jenis_kelamin'] === 'L' ? 'bg-info' : 'bg-pink' ?>" style="<?= $g['jenis_kelamin'] === 'P' ? 'background:#ec4899!important' : '' ?>"><?= $g['jenis_kelamin'] ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editGuru(<?= htmlspecialchars(json_encode($g)) ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <form action="/admin/guru/delete/<?= $g['id'] ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus guru ini?')">
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

<!-- Modal Tambah Guru -->
<div class="modal fade" id="addGuruModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/guru/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NUPTK</label>
                        <input type="text" name="nuptk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="form-select" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
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

<!-- Modal Edit Guru -->
<div class="modal fade" id="editGuruModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editGuruForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Guru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">NUPTK</label>
                        <input type="text" name="nuptk" id="edit_guru_nuptk" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_guru_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis Kelamin</label>
                        <select name="jenis_kelamin" id="edit_guru_jk" class="form-select" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
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
function editGuru(guru) {
    document.getElementById('editGuruForm').action = '/admin/guru/update/' + guru.id;
    document.getElementById('edit_guru_nuptk').value = guru.nuptk;
    document.getElementById('edit_guru_nama').value = guru.nama;
    document.getElementById('edit_guru_jk').value = guru.jenis_kelamin;
    new bootstrap.Modal(document.getElementById('editGuruModal')).show();
}
</script>
<?= $this->endSection() ?>
