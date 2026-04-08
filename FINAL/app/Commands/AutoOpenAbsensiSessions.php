<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AbsensiSessionModel;
use App\Models\JadwalModel;
use App\Models\SemesterModel;
use Config\AbsensiConfig;

class AutoOpenAbsensiSessions extends BaseCommand
{
    protected $group = 'Absensi';
    protected $name = 'absensi:auto-open';
    protected $description = 'Membuka sesi absensi otomatis untuk semua kelas hari ini (Senin-Jumat)';
    protected $usage = 'absensi:auto-open';

    public function run(array $params)
    {
        $config = new AbsensiConfig();

        if (!$config->enableAutoOpen) {
            CLI::write('  [SKIP] Auto-open disabled di config.', 'yellow');
            return;
        }

        // Cek apakah weekend
        $dayOfWeek = date('l');
        if (in_array($dayOfWeek, ['Saturday', 'Sunday'])) {
            CLI::write('  [SKIP] Hari ini weekend (Sabtu/Minggu).', 'yellow');
            return;
        }

        // Cek semester aktif
        $semesterModel = new SemesterModel();
        $semester = $semesterModel->getAktif();
        if (!$semester) {
            CLI::error('Tidak ada semester aktif. Abort.');
            return;
        }

        $tanggal = date('Y-m-d');
        $sessionModel = new AbsensiSessionModel();
        $jadwalModel = new JadwalModel();

        // Cek apakah sudah di-open hari ini
        $existingToday = $sessionModel->where('tanggal', $tanggal)->countAllResults();
        if ($existingToday > 0) {
            CLI::write("  [SKIP] Session untuk {$tanggal} sudah ada ({$existingToday} kelas).", 'yellow');
            return;
        }

        // Ambil semua kelas yang ada jadwal hari ini
        $kelasList = $jadwalModel->getDistinctKelasHariIni($semester['id']);

        if (empty($kelasList)) {
            CLI::write('  [SKIP] Tidak ada jadwal hari ini.', 'yellow');
            return;
        }

        $opened = 0;
        $now = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d ') . $config->autoCloseTime;

        foreach ($kelasList as $row) {
            $kelasId = $row['kelas_id'];

            // Ambil jadwal untuk kelas ini agar dapat guru_id dan jam terakhir
            $jadwal = $jadwalModel->getJadwalKelasTanggal($kelasId, $tanggal, $semester['id']);

            if (empty($jadwal)) continue;

            // Ambil guru_id dari jadwal pertama ( wali kelas )
            $guruId = $jadwal[0]['guru_id'] ?? null;

            // Jam selesai terakhir untuk menghitung expires
            $jamTerakhir = end($jadwal)['jam_selesai'] ?? $config->autoCloseTime;
            $expiresAt = date('Y-m-d ') . $jamTerakhir;

            // Tambah buffer 60 menit
            $expiresAt = date('Y-m-d H:i:s', strtotime($expiresAt) + 3600);
            if (strtotime($expiresAt) < time()) {
                $expiresAt = date('Y-m-d H:i:s', strtotime(date('Y-m-d ') . $config->autoCloseTime));
            }

            // Insert session per kelas (SATU session per kelas per hari)
            $sessionModel->insert([
                'jadwal_id'  => $jadwal[0]['id'], // jadwal pertama sebagai referensi
                'guru_id'    => $guruId,
                'kelas_id'   => $kelasId,
                'rombel_id'  => null,
                'tanggal'    => $tanggal,
                'opened_at'  => $now,
                'expires_at' => $expiresAt,
                'created_at' => $now,
            ]);

            $opened++;
            CLI::write("  [OPEN] Kelas ID {$kelasId} - {$jadwal[0]['nama_kelas']} - expires: {$expiresAt}", 'green');
        }

        CLI::newLine();
        CLI::write("  Total: {$opened} sesi dibuka untuk {$tanggal}", 'green');
    }
}
