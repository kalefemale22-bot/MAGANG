<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>System Settings</h1></div>
</div>

<div class="row g-2">
    <div class="col-lg-4">
        <div class="card-custom mb-2">
            <div class="card-header"><i class="bi bi-lightning me-1"></i>Quick Actions</div>
            <div class="card-body">
                <form action="/dev/settings" method="post" class="mb-2">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="clear_cache">
                    <button type="submit" class="btn btn-warning w-100" onclick="return confirm('Yakin? Semua user aktif akan logout.')">
                        <i class="bi bi-eraser-fill me-1"></i>Clear Cache & Sessions
                    </button>
                </form>
                <small class="text-muted">Menghapus file di writable/cache dan writable/session.</small>
            </div>
        </div>
        <div class="card-custom">
            <div class="card-header"><i class="bi bi-activity me-1"></i>System Status</div>
            <div class="card-body py-2">
                <div class="d-flex justify-content-between mb-1" style="font-size:0.82rem">
                    <span>Environment</span>
                    <span class="badge bg-<?= ENVIRONMENT === 'production' ? 'success' : 'danger' ?>"><?= strtoupper(ENVIRONMENT) ?></span>
                </div>
                <div class="d-flex justify-content-between" style="font-size:0.82rem">
                    <span>PHP Version</span>
                    <span class="badge bg-secondary"><?= phpversion() ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header"><i class="bi bi-file-code me-1"></i>Environment Configuration (.env)</div>
            <div class="card-body">
                <div class="alert alert-info py-1 mb-2" style="font-size:0.8rem">
                    <i class="bi bi-exclamation-triangle me-1"></i>Hati-hati! Perubahan bisa membuat web tidak bisa diakses.
                </div>
                <form action="/dev/settings" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="save_env">
                    <div class="mb-2">
                        <textarea class="form-control text-light bg-dark font-monospace" name="env_content" rows="12" style="font-size:0.78rem;white-space:pre-wrap" spellcheck="false"><?= htmlspecialchars($envContent) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Simpan perubahan .env?')">
                        <i class="bi bi-save me-1"></i>Save Configuration
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
