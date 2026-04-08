<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>System Info</h1></div>
</div>

<div class="row g-2">
    <div class="col-lg-6">
        <div class="card-custom mb-2">
            <div class="card-header"><i class="bi bi-cpu me-1"></i>Server</div>
            <div class="card-body py-2">
                <?php
                $server=['PHP Version'=>$info['php_version'],'CodeIgniter'=>$info['ci_version'],'Server'=>$info['server_software'],
                    'OS'=>$info['os'],'Timezone'=>$info['timezone'],'Memory Limit'=>$info['memory_limit'],
                    'Max Upload'=>$info['max_upload'],'Max POST'=>$info['max_post'],'Max Execution'=>$info['max_execution'].'s'];
                foreach ($server as $k => $v): ?>
                <div style="display:flex;padding:0.4rem 0;border-bottom:1px solid #f1f5f9;font-size:0.82rem">
                    <span style="width:120px;flex-shrink:0;color:#64748b;font-weight:600"><?= $k ?></span>
                    <span style="flex:1;color:#1e293b;font-weight:500"><?= $v ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card-custom mb-2">
            <div class="card-header"><i class="bi bi-database me-1"></i>Database</div>
            <div class="card-body py-2">
                <div style="display:flex;padding:0.4rem 0;border-bottom:1px solid #f1f5f9;font-size:0.82rem"><span style="width:120px;flex-shrink:0;color:#64748b;font-weight:600">Driver</span><span style="flex:1"><?= $info['db_driver'] ?></span></div>
                <div style="display:flex;padding:0.4rem 0;border-bottom:1px solid #f1f5f9;font-size:0.82rem"><span style="width:120px;flex-shrink:0;color:#64748b;font-weight:600">Database</span><span style="flex:1"><code><?= $info['db_name'] ?></code></span></div>
                <div style="display:flex;padding:0.4rem 0;border-bottom:1px solid #f1f5f9;font-size:0.82rem"><span style="width:120px;flex-shrink:0;color:#64748b;font-weight:600">Size</span><span style="flex:1;font-weight:700"><?= number_format($info['db_size']/1024/1024, 2) ?> MB</span></div>
                <div style="display:flex;padding:0.4rem 0;font-size:0.82rem"><span style="width:120px;flex-shrink:0;color:#64748b;font-weight:600">Tables</span><span style="flex:1"><?= count($info['tables']) ?> tabel</span></div>
            </div>
        </div>
        <div class="card-custom">
            <div class="card-header"><i class="bi bi-hdd me-1"></i>Disk Usage</div>
            <div class="card-body py-2">
                <?php $dpct=round((1-$info['disk_free']/$info['disk_total'])*100,1); ?>
                <div class="d-flex justify-content-between mb-1" style="font-size:0.78rem">
                    <span>Used: <strong><?= number_format(($info['disk_total']-$info['disk_free'])/1024/1024/1024,1) ?> GB</strong></span>
                    <span>Free: <strong><?= number_format($info['disk_free']/1024/1024/1024,1) ?> GB</strong></span>
                </div>
                <div style="height:8px;border-radius:4px;background:#e2e8f0;overflow:hidden">
                    <div style="height:100%;width:<?= $dpct ?>%;border-radius:4px;background:<?= $dpct>90?'#ef4444':($dpct>70?'#f59e0b':'#10b981' ?>"></div>
                </div>
                <div class="text-center mt-1" style="font-size:0.7rem;color:#94a3b8"><?= $dpct ?>% used</div>
            </div>
        </div>
    </div>
</div>

<div class="card-custom">
    <div class="card-header"><i class="bi bi-table me-1"></i>Database Tables</div>
    <div class="card-body p-0">
        <div class="table-scroll">
            <table class="table table-custom mb-0">
                <thead><tr><th>Table</th><th class="text-end">Rows</th><th class="text-end">Size</th><th class="text-end">Index</th></tr></thead>
                <tbody>
                <?php foreach ($info['tables'] as $t): ?>
                <tr>
                    <td data-label="Table"><code><?= $t['Name'] ?></code></td>
                    <td data-label="Rows" class="text-end"><?= number_format($t['Rows'] ?? 0) ?></td>
                    <td data-label="Size" class="text-end"><?= number_format(($t['Data_length'] ?? 0)/1024, 1) ?> KB</td>
                    <td data-label="Index" class="text-end"><?= number_format(($t['Index_length'] ?? 0)/1024, 1) ?> KB</td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
