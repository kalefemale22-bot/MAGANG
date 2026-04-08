<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rapor - <?= esc($siswa['nama']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', 'Times New Roman', serif; margin: 0; padding: 0; box-sizing: border-box; }
        body { padding: 20px; font-size: 11pt; color: #1e293b; }

        @media print {
            body { padding: 0; margin: 0; }
            .no-print { display: none !important; }
            @page { margin: 15mm; size: A4; }
        }

        .header { text-align: center; border-bottom: 3px double #1e293b; padding-bottom: 15px; margin-bottom: 20px; }
        .header h2 { font-size: 16pt; font-weight: 800; margin-bottom: 2px; }
        .header h3 { font-size: 12pt; font-weight: 600; margin-bottom: 4px; }
        .header p { font-size: 9pt; color: #64748b; }

        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { padding: 3px 5px; font-size: 10pt; }
        .info-table .label { font-weight: 600; width: 130px; }

        .nilai-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .nilai-table th, .nilai-table td { border: 1px solid #cbd5e1; padding: 6px 8px; font-size: 9pt; }
        .nilai-table th { background: #f1f5f9; font-weight: 700; text-align: center; text-transform: uppercase; font-size: 8pt; }
        .nilai-table td.center { text-align: center; }
        .nilai-table tr:nth-child(even) { background: #fafafa; }
        .nilai-table .predikat-A { color: #16a34a; font-weight: 700; }
        .nilai-table .predikat-B { color: #2563eb; font-weight: 700; }
        .nilai-table .predikat-C { color: #d97706; font-weight: 700; }
        .nilai-table .predikat-D { color: #dc2626; font-weight: 700; }

        .signatures { display: flex; justify-content: space-between; margin-top: 40px; }
        .signature-block { text-align: center; width: 200px; }
        .signature-block .line { border-bottom: 1px solid #1e293b; margin-top: 60px; margin-bottom: 5px; }
        .signature-block .name { font-weight: 700; font-size: 10pt; }
        .signature-block .title { font-size: 9pt; color: #64748b; }

        .btn-print { padding: 10px 30px; background: #4f46e5; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 10pt; }
        .btn-print:hover { background: #3730a3; }
    </style>
</head>
<body>

<div class="no-print" style="text-align:center;margin-bottom:20px">
    <button class="btn-print" onclick="window.print()">🖨️ Print Rapor</button>
    <button class="btn-print" onclick="window.close()" style="background:#64748b;margin-left:10px">✕ Tutup</button>
</div>

<div class="header">
    <h2>SMA NEGERI 6 BANJARMASIN</h2>
    <h3>LAPORAN HASIL BELAJAR PESERTA DIDIK</h3>
    <p>Jl. Brigjen H. Hasan Basry No.7, Banjarmasin • Telp: (0511) 3354xxx</p>
</div>

<table class="info-table">
    <tr>
        <td class="label">Nama Siswa</td>
        <td>: <?= esc($siswa['nama']) ?></td>
        <td class="label">Kelas</td>
        <td>: <?= esc($siswa['nama_kelas'] ?? '-') ?></td>
    </tr>
    <tr>
        <td class="label">NISN</td>
        <td>: <?= esc($siswa['username']) ?></td>
        <td class="label">Semester</td>
        <td>: <?= esc($semester['nama_semester'] ?? '-') ?></td>
    </tr>
    <tr>
        <td class="label">NIS</td>
        <td>: <?= esc($siswa['nis'] ?? '-') ?></td>
        <td class="label">Tahun Pelajaran</td>
        <td>: 2025/2026</td>
    </tr>
</table>

<table class="nilai-table">
    <thead>
        <tr>
            <th style="width:30px">No</th>
            <th style="text-align:left">Mata Pelajaran</th>
            <th>Rata UH</th>
            <th>UTS</th>
            <th>UAS</th>
            <th>Nilai Akhir</th>
            <th>Predikat</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($nilaiPerMapel)): ?>
        <tr><td colspan="7" class="center" style="padding:20px;color:#94a3b8">Belum ada data nilai</td></tr>
        <?php else: ?>
        <?php $no = 1; foreach ($nilaiPerMapel as $mapel => $info): ?>
        <tr>
            <td class="center"><?= $no++ ?></td>
            <td style="font-weight:500"><?= esc($mapel) ?></td>
            <td class="center"><?= $info['rata_uh'] ?: '-' ?></td>
            <td class="center"><?= $info['uts'] ?: '-' ?></td>
            <td class="center"><?= $info['uas'] ?: '-' ?></td>
            <td class="center" style="font-weight:700"><?= $info['nilai_akhir'] ?: '-' ?></td>
            <td class="center predikat-<?= $info['predikat'] ?>"><?= $info['predikat'] ?></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<p style="font-size:9pt;color:#64748b;margin-bottom:5px">
    <strong>Keterangan Predikat:</strong> A (≥88) = Sangat Baik | B (≥75) = Baik | C (≥62) = Cukup | D (<62) = Kurang
</p>
<p style="font-size:9pt;color:#64748b">
    <strong>Rumus Nilai Akhir:</strong> 50% Rata-rata UH + 25% UTS + 25% UAS
</p>

<div class="signatures">
    <div class="signature-block">
        <p class="title">Wali Kelas</p>
        <div class="line"></div>
        <p class="name"><?= esc($waliKelas['nama'] ?? '_______________') ?></p>
        <p class="title">NIP. _______________</p>
    </div>
    <div class="signature-block">
        <p>Banjarmasin, <?= date('d F Y') ?></p>
        <p class="title">Kepala Sekolah</p>
        <div class="line"></div>
        <p class="name">H. Arusliadi, M.Pd</p>
        <p class="title">NIP. _______________</p>
    </div>
</div>

</body>
</html>
