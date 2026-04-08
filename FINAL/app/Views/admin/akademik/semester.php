<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<meta http-equiv="refresh" content="0;url=/admin/akademik/tahun-ajaran">
<div class="text-center py-5">
    <i class="bi bi-arrow-right-circle" style="font-size:2rem;color:var(--primary)"></i>
    <p class="mt-2">Tahun Ajaran & Semester sudah digabung.</p>
    <a href="/admin/akademik/tahun-ajaran" class="btn btn-primary">Buka Pengaturan Akademik</a>
</div>
<?= $this->endSection() ?>
