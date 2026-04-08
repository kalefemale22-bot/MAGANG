<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Terminal</h1></div>
</div>

<div class="card-custom mb-2">
    <div class="card-header bg-dark text-white py-2">
        <span style="font-family:monospace" class="text-success"><i class="bi bi-circle-fill text-danger small me-1"></i><i class="bi bi-circle-fill text-warning small me-1"></i><i class="bi bi-circle-fill text-success small me-1"></i> System Console</span>
    </div>
    <div class="card-body bg-dark p-3">
        <div class="bg-dark text-light p-2 rounded" style="min-height:300px;font-family:'Courier New',monospace;overflow:auto">
            <?php if ($command): ?>
            <div class="mb-2"><span class="text-success"><?= esc($cwd) ?>></span> <?= esc($command) ?></div>
            <?php if ($output): ?>
            <pre class="text-light mb-3" style="white-space:pre-wrap;word-wrap:break-word;font-size:0.8rem"><?= htmlspecialchars($output) ?></pre>
            <?php else: ?>
            <div class="text-muted mb-3" style="font-size:0.8rem">(No output)</div>
            <?php endif; endif; ?>

            <form action="/sys/terminal" method="post" class="mt-3">
                <?= csrf_field() ?>
                <div class="input-group">
                    <span class="input-group-text bg-transparent text-success border-0 px-1" style="font-size:1rem;font-family:monospace">$</span>
                    <input type="text" name="command" class="form-control bg-transparent text-light border-0 shadow-none" placeholder="php spark db:seed" autocomplete="off" style="font-family:monospace;font-size:0.875rem" required>
                    <button class="btn btn-outline-success border-0" type="submit"><i class="bi bi-play-fill"></i></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="alert alert-warning py-2" style="font-size:0.78rem">
    <i class="bi bi-exclamation-triangle me-1"></i><strong>Warning:</strong> Commands dijalankan sebagai web server user. Jangan jalankan `rm -rf` atau perintah interaktif.
</div>

<style>
.bg-dark { background:#1e293b !important; }
.bg-dark input:focus { background:transparent !important; box-shadow:none !important; }
</style>
<script>
document.addEventListener('mouseup', function() { var s = window.getSelection().toString(); if(s==='') document.querySelector('input[name="command"]').focus(); });
</script>

<?= $this->endSection() ?>
