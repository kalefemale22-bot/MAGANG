<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Catatan Rapor & Ekstrakurikuler</h1>
            <p class="text-muted mb-0">Kelas: <strong><?= htmlspecialchars($kelasWali['nama_kelas']) ?></strong> &bull; Semester: <strong><?= htmlspecialchars($semester['nama_semester']) ?></strong></p>
        </div>
    </div>

    <!-- Peringatan Kunci -->
    <?php
        $lockedCount = 0;
        foreach ($existingCatatan as $cat) {
            if ($cat['is_locked']) $lockedCount++;
        }
    ?>
    <?php if ($lockedCount > 0): ?>
        <div class="alert alert-warning d-flex align-items-center mb-4">
            <i class="bi bi-lock-fill me-3 fs-3"></i>
            <div>
                <strong class="d-block">Beberapa data rapor telah dikunci permanen!</strong>
                <span>Ada <?= $lockedCount ?> siswa yang nilai/catatannya sudah berstatus <em>Final</em> dan tidak dapat diedit lagi pada halaman ini. Hubungi Admin jika Anda perlu membuka kuncian data.</span>
            </div>
        </div>
    <?php endif; ?>

    <form action="/guru/catatan-rapor/store" method="post">
        <?= csrf_field() ?>
        
        <div class="card shadow-sm border-0 rounded-3 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Form Input Ekstra & Catatan (Massal)</h6>
                <div class="d-flex gap-2">
                    <?php if ($lockedCount < count($siswaList) && count($siswaList) > 0): ?>
                    <button type="submit" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-save me-1"></i> Simpan Terbuka
                    </button>
                    <button type="button" class="btn btn-danger btn-sm px-3" data-bs-toggle="modal" data-bs-target="#kunciModal">
                        <i class="bi bi-lock-fill me-1"></i> Kunci Rapor Kelas
                    </button>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered align-middle mb-0" style="min-width: 1200px;">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 40px;" rowspan="2">No</th>
                                <th style="width: 15rem;" rowspan="2">Nama Siswa</th>
                                <th class="text-center" colspan="3">Rekap Absensi (Hari)</th>
                                <th class="text-center" colspan="4">Ekstrakurikuler</th>
                                <th style="width: 25rem;" rowspan="2">Catatan Wali Kelas</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 4rem;"><small>Sakit</small></th>
                                <th class="text-center" style="width: 4rem;"><small>Izin</small></th>
                                <th class="text-center" style="width: 4rem;"><small>Alpa</small></th>
                                <th class="text-center"><small>Ekskul 1</small></th>
                                <th class="text-center" style="width: 4rem;"><small>Nilai</small></th>
                                <th class="text-center"><small>Ekskul 2</small></th>
                                <th class="text-center" style="width: 4rem;"><small>Nilai</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1; foreach ($siswaList as $s) : ?>
                                <?php 
                                    $catatan = $existingCatatan[$s['id']] ?? null;
                                    $isLocked = $catatan ? $catatan['is_locked'] : false;
                                    $ekskuls = $existingEkskul[$s['id']] ?? [];
                                    
                                    $eks1 = $ekskuls[0] ?? null;
                                    $eks2 = $ekskuls[1] ?? null;
                                ?>
                                <tr <?= $isLocked ? 'class="table-active bg-light blur-locked"' : '' ?>>
                                    <td class="text-center"><?= $no++ ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($s['nama']) ?></div>
                                        <div class="text-muted" style="font-size:0.75rem;">NISN: <?= htmlspecialchars($s['nisn']) ?></div>
                                        <?php if ($isLocked): ?>
                                            <span class="badge bg-danger mt-1"><i class="bi bi-lock-fill"></i> FINAL</span>
                                        <?php endif; ?>
                                    </td>
                                    
                                    <!-- Absensi Input -->
                                    <td>
                                        <input type="hidden" name="siswa_ids[]" value="<?= $s['id'] ?>">
                                        <input type="number" class="form-control form-control-sm text-center" name="sakit[<?= $s['id'] ?>]" min="0" value="<?= $catatan['sakit'] ?? 0 ?>" <?= $isLocked ? 'readonly tabindex="-1"' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center" name="izin[<?= $s['id'] ?>]" min="0" value="<?= $catatan['izin'] ?? 0 ?>" <?= $isLocked ? 'readonly tabindex="-1"' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="number" class="form-control form-control-sm text-center" name="alpa[<?= $s['id'] ?>]" min="0" value="<?= $catatan['alpa'] ?? 0 ?>" <?= $isLocked ? 'readonly tabindex="-1"' : '' ?>>
                                    </td>
                                    
                                    <!-- Ekskul 1 -->
                                    <td>
                                        <select class="form-select form-select-sm" name="ekskul_1[<?= $s['id'] ?>]" <?= $isLocked ? 'disabled' : '' ?>>
                                            <option value="">-- Pilih --</option>
                                            <?php foreach ($ekskulList as $e): ?>
                                                <option value="<?= $e['id'] ?>" <?= ($eks1 && $eks1['ekskul_id'] == $e['id']) ? 'selected' : '' ?>><?= htmlspecialchars($e['nama_ekskul']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="nilai_ekskul_1[<?= $s['id'] ?>]" <?= $isLocked ? 'disabled' : '' ?>>
                                            <option value=""></option>
                                            <?php foreach(['A','B','C','D'] as $n): ?>
                                                <option value="<?= $n ?>" <?= ($eks1 && $eks1['nilai'] == $n) ? 'selected' : '' ?>><?= $n ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <!-- Ekskul 2 -->
                                    <td>
                                        <select class="form-select form-select-sm" name="ekskul_2[<?= $s['id'] ?>]" <?= $isLocked ? 'disabled' : '' ?>>
                                            <option value="">-- Pilih --</option>
                                            <?php foreach ($ekskulList as $e): ?>
                                                <option value="<?= $e['id'] ?>" <?= ($eks2 && $eks2['ekskul_id'] == $e['id']) ? 'selected' : '' ?>><?= htmlspecialchars($e['nama_ekskul']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="nilai_ekskul_2[<?= $s['id'] ?>]" <?= $isLocked ? 'disabled' : '' ?>>
                                            <option value=""></option>
                                            <?php foreach(['A','B','C','D'] as $n): ?>
                                                <option value="<?= $n ?>" <?= ($eks2 && $eks2['nilai'] == $n) ? 'selected' : '' ?>><?= $n ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>

                                    <!-- Catatan -->
                                    <td>
                                        <textarea class="form-control form-control-sm" name="catatan_wali[<?= $s['id'] ?>]" rows="2" placeholder="Tingkatkan terus belajarmu..." <?= $isLocked ? 'readonly tabindex="-1"' : '' ?>><?= htmlspecialchars($catatan['catatan_wali'] ?? '') ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <?php if (empty($siswaList)): ?>
                                <tr>
                                    <td colspan="10" class="text-center py-5 text-muted">
                                        Tidak ada siswa di kelas Anda.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white py-3 text-end">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Simpan Semua Perubahan
                </button>
            </div>
        </div>
    </form>
</div>

<!-- Modal Kunci Rapor -->
<div class="modal fade" id="kunciModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> Kunci Final Rapor?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/guru/catatan-rapor/kunci" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin mengunci rapor kelas <strong><?= htmlspecialchars($kelasWali['nama_kelas']) ?></strong>?</p>
                    <div class="alert alert-danger px-3 py-2" style="font-size:0.9rem;">
                        <strong>Peringatan Definitif:</strong><br>
                        Setelah dikunci, baik Anda (Wali Kelas) maupun Guru Mata Pelajaran tidak dapat lagi mengubah Nilai, Absensi, atau Catatan untuk siswa di kelas ini pada semester berjalan. Rapor akan dianggap FINAL.
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger px-4">Ya, Kunci Permanen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .blur-locked {
        opacity: 0.8;
        pointer-events: none; /* Cegah klik */
    }
    .blur-locked input, .blur-locked textarea, .blur-locked select {
        background-color: #e9ecef !important;
        border-color: #ced4da !important;
    }
</style>
<?= $this->endSection() ?>
