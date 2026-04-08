<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
.log-viewer { background:#1e293b; color:#e2e8f0; border-radius:8px; padding:0.75rem; font-family:'Courier New',monospace; font-size:0.7rem; line-height:1.6; max-height:400px; overflow-y:auto; white-space:pre-wrap; word-break:break-all; }
.log-viewer .error { color:#f87171; }
.log-viewer .warning { color:#fbbf24; }
.log-viewer .info { color:#60a5fa; }
.log-viewer .debug { color:#a78bfa; }
</style>

<div class="page-header">
    <div><h1>Error Log</h1></div>
</div>

<div class="row g-2">
    <div class="col-lg-3">
        <div class="card-custom mb-2">
            <div class="card-header"><i class="bi bi-folder me-1"></i>Log Files</div>
            <div class="card-body p-0" style="max-height:400px;overflow-y:auto">
                <?php if (empty($logFiles)): ?>
                <div class="text-center text-muted py-3" style="font-size:0.8rem"><i class="bi bi-inbox" style="font-size:1.5rem"></i><br>Tidak ada log</div>
                <?php else: foreach ($logFiles as $f): ?>
                <a href="?file=<?= urlencode($f['name']) ?>" class="list-group-item list-group-item-action <?= ($selectedFile ?? '') === $f['name'] ? 'active' : '' ?>" style="font-size:0.78rem;padding:0.6rem 1rem">
                    <div class="fw-semibold"><?= $f['name'] ?></div>
                    <small class="<?= ($selectedFile ?? '') === $f['name'] ? '' : 'text-muted' ?>"><?= number_format($f['size']/1024, 1) ?> KB · <?= date('d/m H:i', $f['modified']) ?></small>
                </a>
                <?php endforeach; endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card-custom">
            <div class="card-header py-2">
                <div class="d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-terminal me-1"></i><?= esc($selectedFile ?? 'Pilih file') ?></span>
                    <?php if ($selectedFile): ?>
                    <form action="/sys/error-log/clear" method="post" onsubmit="return confirm('Kosongkan file log ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="file" value="<?= esc($selectedFile) ?>">
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <div class="card-body p-0">
                <?php if ($content): ?>
                <div class="log-viewer"><?php
                    foreach (explode("\n", $content) as $line) {
                        $cls = '';
                        if (stripos($line,'ERROR')!==false||stripos($line,'CRITICAL')!==false) $cls='error';
                        elseif (stripos($line,'WARNING')!==false) $cls='warning';
                        elseif (stripos($line,'INFO')!==false) $cls='info';
                        elseif (stripos($line,'DEBUG')!==false) $cls='debug';
                        echo $cls ? "<span class=\"{$cls}\">".esc($line)."</span>\n" : esc($line)."\n";
                    }
                ?></div>
                <?php else: ?>
                <div class="text-center text-muted py-4" style="font-size:0.8rem"><i class="bi bi-file-earmark-x" style="font-size:1.5rem"></i><br>File kosong.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
