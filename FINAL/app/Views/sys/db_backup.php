<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Database Backup</h1></div>
</div>

<div class="row g-2">
    <div class="col-md-6">
        <div class="card-custom text-center py-4">
            <i class="bi bi-database-down" style="font-size:3rem;color:var(--primary)"></i>
            <h5 class="fw-bold mt-2">Backup Database</h5>
            <p class="text-muted mb-3" style="font-size:0.82rem">Download semua tabel dan data dalam format SQL.</p>
            <a href="/sys/db-backup/download" class="btn btn-primary"><i class="bi bi-download me-1"></i>Download (.sql)</a>
            <div class="mt-2" style="font-size:0.7rem;color:#94a3b8">Terakhir: <?= date('d F Y, H:i') ?></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-header"><i class="bi bi-info-circle me-1"></i>Informasi</div>
            <div class="card-body" style="font-size:0.82rem">
                <div class="mb-2"><strong>Yang di-backup:</strong>
                    <ul class="mb-0 ps-3"><li>Struktur & data tabel</li><li>Foreign key constraints</li></ul>
                </div>
                <div class="mb-2"><strong>Cara Restore:</strong>
                    <ol class="mb-0 ps-3"><li>Buka phpMyAdmin</li><li>Pilih database → Import</li><li>Upload file .sql → Execute</li></ol>
                </div>
                <div class="alert alert-warning py-1 mb-0" style="font-size:0.78rem"><i class="bi bi-exclamation-triangle me-1"></i>Backup berkala untuk cegah kehilangan data!</div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
