<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Database Manager</h1></div>
</div>

<div class="row g-2">
    <div class="col-lg-3">
        <div class="card-custom mb-2">
            <div class="card-header"><i class="bi bi-table me-1"></i>Tables (<?= count($tables) ?>)</div>
            <div class="card-body p-0" style="max-height:400px;overflow-y:auto">
                <?php foreach ($tables as $table): ?>
                <a href="?table=<?= urlencode($table) ?>" class="list-group-item list-group-item-action <?= $selectedTable===$table?'active':'' ?>" style="font-size:0.78rem;padding:0.5rem 1rem">
                    <i class="bi bi-table me-1 <?= $selectedTable===$table?'text-white':'text-secondary' ?>"></i><?= esc($table) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-9">
        <div class="card-custom mb-2">
            <div class="card-header py-2"><i class="bi bi-terminal me-1"></i>SQL Runner</div>
            <div class="card-body">
                <form action="/sys/database" method="post">
                    <?= csrf_field() ?>
                    <?php if ($selectedTable): ?>
                    <input type="hidden" name="table" value="<?= esc($selectedTable) ?>">
                    <?php endif; ?>
                    <div class="mb-2">
                        <textarea class="form-control font-monospace" name="custom_query" rows="3" placeholder="SELECT * FROM users..." spellcheck="false" style="font-size:0.8rem;background:#f8fafc"><?= htmlspecialchars($customQuery) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-play-fill me-1"></i>Run</button>
                </form>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-header py-2"><i class="bi bi-table me-1"></i>Result</div>
            <div class="card-body p-0">
                <?php if ($queryError): ?>
                <div class="alert alert-danger m-2"><i class="bi bi-exclamation-triangle me-1"></i><?= esc($queryError) ?></div>
                <?php elseif ($queryResult !== null): ?>
                    <?php if ($queryType === 'action'): ?>
                    <div class="alert alert-success m-2"><i class="bi bi-check-circle me-1"></i>Berhasil. <?= $queryResult ?> row(s) affected.</div>
                    <?php elseif ($queryType==='select' && count($queryResult)===0): ?>
                    <div class="alert alert-info m-2"><i class="bi bi-info-circle me-1"></i>No records found.</div>
                    <?php elseif ($queryType==='select' && count($queryResult)>0): ?>
                    <div class="table-scroll" style="max-height:350px;overflow:auto">
                        <table class="table table-custom mb-0" style="font-size:0.75rem">
                            <thead><tr><?php foreach (array_keys($queryResult[0]) as $col): ?><th class="font-monospace"><?= esc($col) ?></th><?php endforeach; ?></tr></thead>
                            <tbody>
                                <?php foreach ($queryResult as $row): ?>
                                <tr>
                                    <?php foreach ($row as $val): ?>
                                    <td style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="<?= esc((string)$val) ?>">
                                        <?php if ($val===null): ?><span class="text-muted">NULL</span><?= esc(mb_strimwidth((string)$val,0,30,'...')) ?><?php endif; ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-2 py-1" style="font-size:0.7rem;color:#94a3b8"><?= count($queryResult) ?> row(s)</div>
                    <?php endif; ?>
                <?php else: ?>
                <div class="text-center text-muted py-4" style="font-size:0.82rem"><i class="bi bi-database" style="font-size:1.5rem"></i><br>Select a table or run a query.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
