<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\AbsensiSessionModel;
use App\Models\AbsensiSiswaModel;
use App\Models\SiswaModel;
use App\Models\JadwalModel;
use App\Models\SemesterModel;
use Config\AbsensiConfig;

class AutoCloseAbsensiSessions extends BaseCommand
{
    protected $group = 'Absensi';
    protected $name = 'absensi:auto-close';
    protected $description = 'Menutup sesi absensi otomatis & menandai Alpha siswa yang belum mengisi';
    protected $usage = 'absensi:auto-close';

    public function run(array $params)
    {
        $config = new AbsensiConfig();

        if (!$config->enableAutoClose) {
            CLI::write('  [SKIP] Auto-close disabled di config.', 'yellow');
            return;
        }

        $tanggal = date('Y-m-d');
        $sessionModel = new AbsensiSessionModel();
        $absensiModel = new AbsensiSiswaModel();
        $siswaModel = new SiswaModel();
        $jadwalModel = new JadwalModel();
        $semesterModel = new SemesterModel();
        $semester = $semesterModel->getAktif();

        if (!$semester) {
            CLI::error('Tidak ada semester aktif. Abort.');
            return;
        }

        // Ambil semua session aktif hari ini
        $sessions = $sessionModel->getAllActiveSessionsToday();

        if (empty($sessions)) {
            CLI::write('  [SKIP] Tidak ada sesi aktif untuk ditutup.', 'yellow');
            return;
        }

        $totalAlpha = 0;

        foreach ($sessions as $session) {
            $kelasId = $session['kelas_id'];
            $sessionTanggal = $session['tanggal'];

            // Ambil semua siswa aktif di kelas ini
            $siswaList = $siswaModel->getByKelas($kelasId);

            // Ambil semua jadwal kelas ini hari ini
            $jadwalList = $jadwalModel->getJadwalKelasTanggal($kelasId, $sessionTanggal, $semester['id']);

            if (empty($jadwalList)) continue;

            // Untuk setiap jadwal: cek siswa yang belum ada absensi → Alpha
            foreach ($jadwalList as $jadwal) {
                $jid = $jadwal['id'];
                $guruId = $jadwal['guru_id'];

                foreach ($siswaList as $siswa) {
                    // Cek apakah siswa sudah mengisi untuk jadwal ini
                    $existing = $absensiModel->where('siswa_id', $siswa['id'])
                        ->where('jadwal_id', $jid)
                        ->where('tanggal', $sessionTanggal)
                        ->first();

                    if (!$existing) {
                        $absensiModel->insert([
                            'siswa_id'   => $siswa['id'],
                            'kelas_id'   => $kelasId,
                            'rombel_id'  => null,
                            'jadwal_id'  => $jid,
                            'tanggal'    => $sessionTanggal,
                            'status'     => 'Alpha',
                            'keterangan' => 'Otomatis alfa (tidak mengisi form absensi)',
                            'guru_id'    => $guruId,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                        $totalAlpha++;
                    }
                }
            }

            // Hapus session setelah ditutup
            $sessionModel->deleteSessionByKelas($kelasId, $sessionTanggal);
            CLI::write("  [CLOSE] Kelas ID {$kelasId} - {$jadwalList[0]['nama_kelas']} - {$totalAlpha} siswa di-alpha-kan", 'red');
        }

        CLI::newLine();
        CLI::write("  Selesai. Total siswa di-alpha-kan: {$totalAlpha}", 'green');
    }
}
