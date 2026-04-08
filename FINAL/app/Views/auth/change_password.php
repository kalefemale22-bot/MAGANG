<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Ganti Password</h1></div>
</div>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">
        <div class="card-custom">
            <?php if (session()->get('is_first_login')): ?>
            <div class="alert alert-warning m-3 mb-0 mt-3">
                <i class="bi bi-exclamation-triangle me-1"></i>Password default masih aktif. Silakan ganti untuk keamanan.
            </div>
            <?php endif; ?>
            <div class="card-body p-3">
                <form method="post" action="/auth/change-password">
                    <?= csrf_field() ?>
                    <div class="mb-2">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="password_baru" required minlength="6" placeholder="Minimal 6 karakter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password</label>
                        <input type="password" class="form-control" name="konfirmasi" required placeholder="Ketik ulang password baru">
                    </div>
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-check-lg me-1"></i>Simpan Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
