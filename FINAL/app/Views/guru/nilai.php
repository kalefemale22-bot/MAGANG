<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Input Nilai</h1><p>Pilih kelas dan mapel</p></div>
</div>

<div class="card-custom">
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
        <div class="row g-2">
            <?php foreach ($assignments as $a): ?>
            <div class="col-6 col-md-4">
                <a href="/guru/nilai/input/<?= $a['kelas']['id'] ?>/<?= $a['mapel']['id'] ?>" class="text-decoration-none">
                    <div class="stat-card">
                        <div class="stat-icon" style="background:rgba(14,165,233,0.12);color:#0ea5e9"><i class="bi bi-journal-text"></i></div>
                        <div class="stat-body">
                            <div class="stat-value" style="font-size:1rem"><?= esc($a['kelas']['nama_kelas']) ?></div>
                            <div class="stat-label"><?= esc($a['mapel']['nama']) ?></div>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state"><i class="bi bi-journal-x"></i><h5>Belum Ada Jadwal</h5><p>Hubungi admin.</p></div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
