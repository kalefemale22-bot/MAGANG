<?php

namespace App\Controllers\Siswa;

use App\Controllers\BaseController;
use App\Models\{SiswaModel, KelasModel, JadwalModel, SemesterModel, NilaiModel, AbsensiSiswaModel, LaporanKehadiranGuruModel, MapelModel, ActivityLogModel};

class SiswaController extends BaseController
{
    protected $siswa;
    protected $semester;

    public function __construct()
    {
        $this->siswa = (new SiswaModel())->getWithKelas(session()->get('entity_id'));
        $this->semester = (new SemesterModel())->getAktif();
    }

    public function profil()
    {
        $kelas = $this->siswa['kelas_id'] ? (new KelasModel())->find($this->siswa['kelas_id']) : null;
        return view('siswa/profil', [
            'title' => 'Profil Saya',
            'siswa' => $this->siswa,
            'kelas' => $kelas,
        ]);
    }

    public function index()
    {
        $jadwalModel = new JadwalModel();
        $jadwalHariIni = [];

        if ($this->siswa && $this->siswa['kelas_id'] && $this->semester) {
            $jadwalHariIni = $jadwalModel->getJadwalHariIni($this->siswa['kelas_id'], [], $this->semester['id']);
        }

        // Count absensi
        $absensiModel = new AbsensiSiswaModel();
        $totalHadir = $absensiModel->where('siswa_id', $this->siswa['id'])->where('status', 'Hadir')->countAllResults();
        $totalAbsen = $absensiModel->where('siswa_id', $this->siswa['id'])->countAllResults();

        $data = [
            'title' => 'Dashboard Siswa',
            'siswa' => $this->siswa,
            'jadwalHariIni' => $jadwalHariIni,
            'totalHadir' => $totalHadir,
            'totalAbsen' => $totalAbsen,
            'semester' => $this->semester,
        ];
        return view('siswa/dashboard', $data);
    }

    public function jadwal()
    {
        $jadwalModel = new JadwalModel();
        $jadwal = [];
        if ($this->siswa && $this->siswa['kelas_id'] && $this->semester) {
            $jadwal = $jadwalModel->getJadwalKelas($this->siswa['kelas_id'], $this->semester['id']);
        }

        $data = [
            'title' => 'Jadwal Pelajaran',
            'jadwal' => $jadwal,
            'siswa' => $this->siswa,
        ];
        return view('siswa/jadwal', $data);
    }

    public function nilai()
    {
        $nilaiModel = new NilaiModel();
        $mapelModel = new MapelModel();

        $nilai = [];
        if ($this->semester) {
            $nilai = $nilaiModel->select('nilai.*, mata_pelajaran.nama as mapel_nama, mata_pelajaran.kode as mapel_kode')
                ->join('mata_pelajaran', 'mata_pelajaran.id = nilai.mapel_id')
                ->where('nilai.siswa_id', $this->siswa['id'])
                ->where('nilai.semester_id', $this->semester['id'])
                ->orderBy('mata_pelajaran.nama')
                ->orderBy('nilai.jenis_nilai')
                ->findAll();
        }

        // Group by mapel
        $nilaiPerMapel = [];
        foreach ($nilai as $n) {
            $nilaiPerMapel[$n['mapel_nama']][$n['jenis_nilai']] = $n['nilai'];
        }

        $data = [
            'title' => 'Nilai & Rapor',
            'nilaiPerMapel' => $nilaiPerMapel,
            'siswa' => $this->siswa,
            'jenisNilai' => ['UH1', 'UH2', 'UH3', 'UTS', 'UAS', 'Tugas', 'Praktik'],
        ];
        return view('siswa/nilai', $data);
    }

    public function absensi()
    {
        $absensiModel = new AbsensiSiswaModel();

        $absensi = $absensiModel->select('absensi_siswa.*, mata_pelajaran.nama as mapel_nama, kelas.nama_kelas')
            ->join('jadwal', 'jadwal.id = absensi_siswa.jadwal_id')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('kelas', 'kelas.id = absensi_siswa.kelas_id', 'left')
            ->where('absensi_siswa.siswa_id', $this->siswa['id'])
            ->orderBy('absensi_siswa.tanggal', 'DESC')
            ->limit(100)
            ->findAll();

        // Rekap
        $rekap = $absensiModel->select('status, COUNT(*) as total')
            ->where('siswa_id', $this->siswa['id'])
            ->groupBy('status')
            ->findAll();

        $data = [
            'title' => 'Rekap Absensi',
            'absensi' => $absensi,
            'rekap' => $rekap,
            'siswa' => $this->siswa,
        ];
        return view('siswa/absensi', $data);
    }

    public function rapor()
    {
        $nilaiModel = new NilaiModel();

        $nilaiPerMapel = [];
        if ($this->semester) {
            $nilai = $nilaiModel->select('nilai.*, mata_pelajaran.nama as mapel_nama, mata_pelajaran.kode as mapel_kode, mata_pelajaran.kelompok, mata_pelajaran.bobot_uh, mata_pelajaran.bobot_tugas, mata_pelajaran.bobot_praktik, mata_pelajaran.bobot_uts, mata_pelajaran.bobot_uas')
                ->join('mata_pelajaran', 'mata_pelajaran.id = nilai.mapel_id')
                ->where('nilai.siswa_id', $this->siswa['id'])
                ->where('nilai.semester_id', $this->semester['id'])
                ->orderBy('mata_pelajaran.kelompok')
                ->orderBy('mata_pelajaran.nama')
                ->findAll();

            foreach ($nilai as $n) {
                $nilaiPerMapel[$n['mapel_nama']]['values'][$n['jenis_nilai']] = $n['nilai'];
                $nilaiPerMapel[$n['mapel_nama']]['kode'] = $n['mapel_kode'];
                $nilaiPerMapel[$n['mapel_nama']]['kelompok'] = $n['kelompok'];
                
                // Store weights
                $nilaiPerMapel[$n['mapel_nama']]['bobot_uh'] = $n['bobot_uh'] ?? 50;
                $nilaiPerMapel[$n['mapel_nama']]['bobot_tugas'] = $n['bobot_tugas'] ?? 0;
                $nilaiPerMapel[$n['mapel_nama']]['bobot_praktik'] = $n['bobot_praktik'] ?? 0;
                $nilaiPerMapel[$n['mapel_nama']]['bobot_uts'] = $n['bobot_uts'] ?? 25;
                $nilaiPerMapel[$n['mapel_nama']]['bobot_uas'] = $n['bobot_uas'] ?? 25;
            }

            foreach ($nilaiPerMapel as $mapel => &$info) {
                $vals = $info['values'];
                $uhVals = array_filter([$vals['UH1'] ?? null, $vals['UH2'] ?? null, $vals['UH3'] ?? null]);
                $info['rata_uh'] = count($uhVals) > 0 ? round(array_sum($uhVals) / count($uhVals), 2) : 0;
                $info['tugas'] = $vals['Tugas'] ?? 0;
                $info['praktik'] = $vals['Praktik'] ?? 0;
                $info['uts'] = $vals['UTS'] ?? 0;
                $info['uas'] = $vals['UAS'] ?? 0;
                
                // Dynamic weight calculation
                $info['nilai_akhir'] = round(
                    ($info['rata_uh'] * ($info['bobot_uh'] / 100)) + 
                    ($info['tugas'] * ($info['bobot_tugas'] / 100)) + 
                    ($info['praktik'] * ($info['bobot_praktik'] / 100)) + 
                    ($info['uts'] * ($info['bobot_uts'] / 100)) + 
                    ($info['uas'] * ($info['bobot_uas'] / 100)),
                2);
                
                $na = $info['nilai_akhir'];
                $info['predikat'] = $na >= 88 ? 'A' : ($na >= 75 ? 'B' : ($na >= 62 ? 'C' : 'D'));
            }
        }

        $data = [
            'title'        => 'Rapor Saya',
            'siswa'        => $this->siswa,
            'semester'     => $this->semester,
            'nilaiPerMapel' => $nilaiPerMapel,
            'jenisNilai'   => ['UH1', 'UH2', 'UH3', 'UTS', 'UAS', 'Tugas', 'Praktik'],
        ];
        return view('siswa/rapor', $data);
    }

    public function laporanGuru()
    {
        $jadwalModel  = new JadwalModel();
        $sessionModel = new \App\Models\AbsensiSessionModel();
        $absensiModel = new AbsensiSiswaModel();
        $laporanModel = new LaporanKehadiranGuruModel();

        $tanggal = date('Y-m-d');
        $now = time();
        $data = [
            'title' => 'Absensi & Laporan Kehadiran',
            'siswa' => $this->siswa,
            'tanggal' => $tanggal,
            'session' => null,
            'is_open' => false,
            'expired' => false,
            'sudah_absen' => false,
            'sudah_dilaporkan' => false,
            'laporan' => null,
            'jadwalList' => [],
            'sisa_detik' => 0,
            'batas_waktu' => '',
        ];

        if ($this->siswa && $this->siswa['kelas_id'] && $this->semester) {
            // Cek session aktif untuk kelas siswa
            $session = $sessionModel->getActiveSessionForKelas($this->siswa['kelas_id'], $tanggal);

            // Cek apakah siswa sudah mengisi minimal SATU jadwal hari ini
            $jadwalHariIni = $jadwalModel->getJadwalSiswaHariIni($this->siswa['id'], $tanggal, $this->semester['id']);

            // Cek apakah siswa sudah mengisi (cukup cek jadwal pertama)
            if (!empty($jadwalHariIni)) {
                $firstJadwal = $jadwalHariIni[0];
                $existingAbsen = $absensiModel->where('siswa_id', $this->siswa['id'])
                    ->where('jadwal_id', $firstJadwal['id'])
                    ->where('tanggal', $tanggal)
                    ->where('status', 'Hadir')
                    ->first();
                $data['sudah_absen'] = $existingAbsen !== null;

                // Cek laporan guru (monitoring)
                if (!empty($this->siswa['is_monitoring'])) {
                    $existingLaporan = $laporanModel->where('siswa_id', $this->siswa['id'])
                        ->where('tanggal', $tanggal)
                        ->first();
                    $data['sudah_dilaporkan'] = $existingLaporan !== null;
                    $data['laporan'] = $existingLaporan;
                }
            }

            // Hitung status session
            if ($session) {
                $expiresWaktu = strtotime($session['expires_at']);
                $data['session'] = $session;
                $data['batas_waktu'] = date('H:i', $expiresWaktu);
                if ($now <= $expiresWaktu) {
                    $data['is_open'] = true;
                    $data['sisa_detik'] = max(0, $expiresWaktu - $now);
                } else {
                    $data['expired'] = true;
                }
            }

            $data['jadwalList'] = $jadwalHariIni;
        }

        return view('siswa/laporan_guru', $data);
    }

    public function storeLaporanGuru()
    {
        $jadwalModel  = new JadwalModel();
        $sessionModel = new \App\Models\AbsensiSessionModel();
        $absensiModel = new AbsensiSiswaModel();
        $laporanModel = new LaporanKehadiranGuruModel();

        $tanggal = date('Y-m-d');
        $kelasId = $this->siswa['kelas_id'] ?? null;
        $status = $this->request->getPost('status'); // untuk laporan guru (monitoring)
        $keterangan = $this->request->getPost('keterangan');

        // Validasi: session aktif untuk kelas siswa
        if (!$kelasId || !$sessionModel->isSessionActiveForKelas($kelasId, $tanggal)) {
            return redirect()->back()->with('error', 'Sesi absensi belum dibuka atau sudah ditutup.');
        }

        // Cek apakah sudah mengisi
        $jadwalHariIni = $jadwalModel->getJadwalSiswaHariIni($this->siswa['id'], $tanggal, $this->semester['id'] ?? 0);
        if (empty($jadwalHariIni)) {
            return redirect()->back()->with('error', 'Tidak ada jadwal untuk hari ini.');
        }

        // Cek sudah mengisi atau belum (cek jadwal pertama)
        $firstJadwal = $jadwalHariIni[0];
        $existingAbsen = $absensiModel->where('siswa_id', $this->siswa['id'])
            ->where('jadwal_id', $firstJadwal['id'])
            ->where('tanggal', $tanggal)
            ->first();

        if ($existingAbsen) {
            return redirect()->back()->with('info', 'Anda sudah mengisi kehadiran hari ini.');
        }

        $insertedCount = 0;
        $firstGuruId = $firstJadwal['guru_id'] ?? null;
        $firstMapelId = $firstJadwal['mapel_id'] ?? null;

        // Insert absensi Hadir untuk SETIAP jadwal siswa hari ini (SEMUA mapel)
        foreach ($jadwalHariIni as $jadwal) {
            $absensiModel->insert([
                'siswa_id'   => $this->siswa['id'],
                'jadwal_id'  => $jadwal['id'],
                'kelas_id'   => $kelasId,
                'rombel_id'  => null,
                'guru_id'    => $jadwal['guru_id'],
                'tanggal'    => $tanggal,
                'status'     => 'Hadir',
                'keterangan' => 'Diisi mandiri via form absensi',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $insertedCount++;
        }

        // Insert laporan guru jika monitoring aktif (sekali saja, tidak per jadwal)
        if (!empty($this->siswa['is_monitoring'])) {
            $existingLaporan = $laporanModel->where('siswa_id', $this->siswa['id'])
                ->where('tanggal', $tanggal)
                ->first();

            if (!$existingLaporan) {
                $laporanModel->insert([
                    'siswa_id'    => $this->siswa['id'],
                    'jadwal_id'  => $firstJadwal['id'],
                    'guru_id'    => $firstGuruId,
                    'mapel_id'   => $firstMapelId,
                    'kelas_id'   => $kelasId,
                    'rombel_id'  => null,
                    'tanggal'    => $tanggal,
                    'status'     => $status ?: 'hadir',
                    'keterangan' => $keterangan,
                    'is_verified' => 0,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Hapus session agar tidak bisa isi 2x
        $sessionModel->deleteSessionByKelas($kelasId, $tanggal);

        (new ActivityLogModel())->logActivity(session()->get('user_id'), 'laporan_kehadiran_guru', 'isi_kehadiran_harian', null, ['jumlah_mapel' => $insertedCount]);

        return redirect()->to('/siswa/laporan-guru')->with('success', "Kehadiran berhasil dicatat untuk {$insertedCount} mapel hari ini.");
    }
}

