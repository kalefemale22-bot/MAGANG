<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold">Data Kelas <span class="badge bg-purple ms-2"><?= count($kelasList) ?></span></h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addKelasModal">
        <i class="bi bi-plus-lg me-1"></i> Tambah Kelas
    </button>
</div>

<div class="row g-3">
    <?php foreach ($kelasList as $k): ?>
    <div class="col-md-4 col-lg-3">
        <div class="stat-card h-100">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <div class="stat-value" style="font-size:1.3rem"><?= esc($k['nama_kelas']) ?></div>
                    <span class="badge bg-<?= $k['tingkat'] === 'X' ? 'info' : ($k['tingkat'] === 'XI' ? 'warning' : 'success') ?>"><?= $k['tingkat'] ?></span>
                    <?php if ($k['jurusan']): ?>
                    <span class="badge bg-purple"><?= $k['jurusan'] ?></span>
                    <?php endif; ?>
                </div>
                <div class="stat-icon" style="background:rgba(79,70,229,0.1);color:var(--primary)">
                    <i class="bi bi-building"></i>
                </div>
            </div>
            <div class="mb-2" style="font-size:0.85rem">
                <i class="bi bi-person-badge me-1 text-muted"></i>
                <span class="text-muted">Wali: </span>
                <span class="fw-semibold"><?= esc($k['wali_nama'] ?? 'Belum diset') ?></span>
            </div>
            <div class="mb-3" style="font-size:0.85rem">
                <i class="bi bi-people me-1 text-muted"></i>
                <span class="text-muted">Siswa: </span>
                <span class="fw-semibold"><?= $k['jumlah_siswa'] ?? 0 ?></span>
            </div>
            <div class="d-flex gap-1">
                <button class="btn btn-sm btn-outline-primary flex-fill" onclick='editKelas(<?= json_encode($k) ?>)'>
                    <i class="bi bi-pencil me-1"></i>Edit
                </button>
                <form action="/admin/kelas/delete/<?= $k['id'] ?>" method="post" onsubmit="return confirm('Hapus kelas ini?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal Tambah Kelas -->
<div class="modal fade" id="addKelasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/kelas/store" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" class="form-control" placeholder="contoh: X-8" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tingkat</label>
                        <select name="tingkat" class="form-select" required>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jurusan</label>
                        <select name="jurusan" class="form-select">
                            <option value="">Tidak Ada</option>
                            <option value="MIPA">MIPA</option>
                            <option value="IPS">IPS</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wali Kelas</label>
                        <select name="wali_kelas_id" class="form-select" id="add_kelas_wali">
                            <option value="">Belum dipilih</option>
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

<!-- Modal Edit Kelas -->
<div class="modal fade" id="editKelasModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editKelasForm" method="post">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Edit Kelas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Kelas</label>
                        <input type="text" name="nama_kelas" id="edit_kelas_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tingkat</label>
                        <select name="tingkat" id="edit_kelas_tingkat" class="form-select" required>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jurusan</label>
                        <select name="jurusan" id="edit_kelas_jurusan" class="form-select">
                            <option value="">Tidak Ada</option>
                            <option value="MIPA">MIPA</option>
                            <option value="IPS">IPS</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Wali Kelas</label>
                        <select name="wali_kelas_id" id="edit_kelas_wali" class="form-select">
                            <option value="">Belum dipilih</option>
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
// Guru list for wali kelas dropdown
const guruList = <?= json_encode(array_map(function($k) {
    return ['id' => $k['wali_kelas_id'] ?? null, 'nama' => $k['wali_nama'] ?? ''];
}, $kelasList)) ?>;

// Fetch guru list dynamically using stored data
<?php
$guruModel = new \App\Models\GuruModel();
$allGuru = $guruModel->orderBy('nama')->findAll();
?>
const allGuru = <?= json_encode($allGuru) ?>;

// Populate wali dropdowns
function populateWali(selectId, selectedId) {
    const sel = document.getElementById(selectId);
    sel.innerHTML = '<option value="">Belum dipilih</option>';
    allGuru.forEach(g => {
        const opt = document.createElement('option');
        opt.value = g.id;
        opt.textContent = g.nama;
        if (g.id == selectedId) opt.selected = true;
        sel.appendChild(opt);
    });
}

document.addEventListener('DOMContentLoaded', () => populateWali('add_kelas_wali', ''));

function editKelas(k) {
    document.getElementById('editKelasForm').action = '/admin/kelas/update/' + k.id;
    document.getElementById('edit_kelas_nama').value = k.nama_kelas;
    document.getElementById('edit_kelas_tingkat').value = k.tingkat;
    document.getElementById('edit_kelas_jurusan').value = k.jurusan || '';
    populateWali('edit_kelas_wali', k.wali_kelas_id);
    new bootstrap.Modal(document.getElementById('editKelasModal')).show();
}
</script>
<?= $this->endSection() ?>
