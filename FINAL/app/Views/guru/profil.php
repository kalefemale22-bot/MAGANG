<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="page-header">
    <div><h1>Profil Saya</h1></div>
</div>

<!-- Profile Banner -->
<div style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);border-radius:12px;overflow:hidden;margin-bottom:1rem">
    <div style="padding:1.5rem;display:flex;align-items:center;gap:1rem;flex-wrap:wrap">
        <div style="width:70px;height:70px;border-radius:50%;border:3px solid rgba(255,255,255,0.4);overflow:hidden;background:rgba(255,255,255,0.2);display:flex;align-items:center;justify-content:center;font-size:1.8rem;font-weight:700;color:#fff;flex-shrink:0">
            <?php if (!empty($guru['foto'])): ?>
                <img src="/uploads/foto/<?= esc($guru['foto']) ?>" style="width:100%;height:100%;object-fit:cover" alt="foto">
            <?php else: ?>
                <?= strtoupper(substr($guru['nama'], 0, 1)) ?>
            <?php endif; ?>
        </div>
        <div style="color:#fff;flex:1;min-width:150px">
            <div style="font-weight:700;font-size:1.2rem"><?= esc($guru['nama']) ?></div>
            <div style="display:flex;flex-wrap:wrap;gap:0.3rem;margin-top:0.3rem">
                <span style="background:rgba(255,255,255,0.2);border-radius:20px;padding:0.15rem 0.6rem;font-size:0.7rem;font-weight:600">Guru</span>
                <?php if (!empty($guru['status_kepegawaian'])): ?>
                <span style="background:rgba(255,255,255,0.2);border-radius:20px;padding:0.15rem 0.6rem;font-size:0.7rem;font-weight:600"><?= esc($guru['status_kepegawaian']) ?></span>
                <?php endif; ?>
            </div>
            <div style="font-size:0.72rem;color:rgba(255,255,255,0.8);margin-top:0.3rem">
                NUPTK: <?= esc($guru['nuptk'] ?? '-') ?> <?= !empty($guru['email']) ? '| ' . esc($guru['email']) : '' ?>
            </div>
        </div>
    </div>
</div>

<div class="row g-2">
    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-header"><i class="bi bi-person-vcard me-1"></i>Data Pribadi</div>
            <div class="card-body py-2">
                <?php
                $pribadi = ['NUPTK'=>$guru['nuptk']??null,'NIK'=>$guru['nik']??null,'Nama'=>$guru['nama']??null,
                    'JK'=>($guru['jenis_kelamin']??'')==='L'?'Laki-laki':'Perempuan',
                    'TTL'=>($guru['tempat_lahir']??'').($guru['tanggal_lahir']?', '.date('d/m/Y',strtotime($guru['tanggal_lahir'])):''),
                    'Agama'=>$guru['agama']??null];
                foreach ($pribadi as $label => $value): ?>
                <div style="display:flex;padding:0.5rem 0;border-bottom:1px solid #f1f5f9;font-size:0.82rem">
                    <span style="width:90px;flex-shrink:0;color:#64748b;font-weight:600"><?= $label ?></span>
                    <span style="flex:1;color:#1e293b"><?= $value ?: '<span style="color:#cbd5e1;font-style:italic">Belum diisi</span>' ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card-custom">
            <div class="card-header"><i class="bi bi-telephone me-1"></i>Kontak & Kepegawaian</div>
            <div class="card-body py-2">
                <?php
                $kontak = ['HP'=>$guru['no_hp']??null,'Email'=>$guru['email']??null,'Alamat'=>$guru['alamat']??null,
                    'Status'=>$guru['status_kepegawaian']??null,'Jabatan'=>$guru['jabatan']??null,
                    'Gelar Depan'=>$guru['gelar_depan']??null,'Gelar Belakang'=>$guru['gelar_belakang']??null];
                foreach ($kontak as $label => $value): ?>
                <div style="display:flex;padding:0.5rem 0;border-bottom:1px solid #f1f5f9;font-size:0.82rem">
                    <span style="width:90px;flex-shrink:0;color:#64748b;font-weight:600"><?= $label ?></span>
                    <span style="flex:1;color:#1e293b"><?= $value ?: '<span style="color:#cbd5e1;font-style:italic">Belum diisi</span>' ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-2 py-2" style="font-size:0.82rem">
    <i class="bi bi-cloud-download me-1"></i>Data profil terisi otomatis dari Sinkronisasi Dapodik oleh admin.
</div>

<?= $this->endSection() ?>
