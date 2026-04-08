<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>File Manager</h1><p style="font-size:0.78rem;color:#64748b">Path: <code><?= esc($targetPath) ?></code></p></div>
</div>

<div class="card-custom">
    <div class="card-body p-0">
        <?php if ($isDirectory): ?>
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>Name</th><th>Size</th><th>Modified</th></tr></thead>
                <tbody>
                    <?php foreach ($items as $item):
                        $path = $targetPath . DIRECTORY_SEPARATOR . $item;
                        $isDir = is_dir($path);
                        $relPath = ltrim(str_replace($basePath, '', $path), '\\/');
                    ?>
                    <tr>
                        <td data-label="Name">
                            <?php if ($isDir): ?>
                            <i class="bi bi-folder-fill text-warning me-1"></i>
                            <a href="?path=<?= urlencode($relPath) ?>" class="text-decoration-none fw-semibold"><?= esc($item) ?></a>
                            <?php else: ?>
                            <i class="bi bi-file-earmark-code text-secondary me-1"></i>
                            <a href="?path=<?= urlencode($relPath) ?>" class="text-decoration-none"><?= esc($item) ?></a>
                            <?php endif; ?>
                        </td>
                        <td data-label="Size"><?= $isDir ? '-' : round(filesize($path)/1024, 2).' KB' ?></td>
                        <td data-label="Modified" style="font-size:0.75rem;color:#64748b"><?= date('Y-m-d H:i', filemtime($path)) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-3">
            <div class="mb-2">
                <a href="?path=<?= urlencode(dirname($currentPath) == '.' ? '' : dirname($currentPath)) ?>" class="btn btn-sm btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            </div>
            <form action="/sys/file-manager" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="save">
                <input type="hidden" name="file" value="<?= esc($currentPath) ?>">
                <div class="mb-2">
                    <textarea class="form-control text-light bg-dark font-monospace" name="content" rows="20" style="font-size:0.78rem;white-space:pre-wrap" spellcheck="false"><?= htmlspecialchars($fileContent) ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Save</button>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
