<?php

namespace App\Controllers\Guru;

use App\Controllers\BaseController;
use App\Models\{GuruModel, SiswaModel, KelasModel, MapelModel, JadwalModel, SemesterModel, AbsensiSiswaModel, NilaiModel, ActivityLogModel};

class GuruController extends BaseController
{
    protected $guru;
    protected $semester;

    public function __construct()
    {
        $this->guru = (new GuruModel())->find(session()->get('entity_id'));
        $this->semester = (new SemesterModel())->getAktif();
    }

    public function profil()
    {
        return view('guru/profil', [
            'title' => 'Profil Saya',
            'guru' => $this->guru,
        ]);
    }

    public function index()
    {
        $jadwalModel = new JadwalModel();
        $kelasModel = new KelasModel();

        // Wali kelas
        $waliKelas = (new GuruModel())->getWaliKelas($this->guru['id']);

        // Jadwal hari ini
        $jadwalHariIni = [];
        if ($this->semester) {
            $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat'];
            $hariIni = $hariMap[date('l')] ?? null;

            if ($hariIni) {
                $jadwalHariIni = $jadwalModel->select('jadwal.*, mata_pelajaran.nama as mapel_nama, kelas.nama_kelas, rombel.nama_rombel')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                    ->join('kelas', 'kelas.id = jadwal.kelas_id', 'left')
                    ->join('rombel', 'rombel.id = jadwal.rombel_id', 'left')
                    ->where('jadwal.guru_id', $this->guru['id'])
                    ->where('jadwal.semester_id', $this->semester['id'])
                    ->where('jadwal.hari', $hariIni)
                    ->orderBy('jadwal.jam_ke')
                    ->findAll();
            }
        }

        // Total kelas diajar
        $kelasCount = $jadwalModel->select('DISTINCT COALESCE(kelas_id, rombel_id) as target')
            ->where('guru_id', $this->guru['id'])
            ->where('semester_id', $this->semester['id'] ?? 0)
            ->countAllResults();

        $data = [
            'title' => 'Dashboard Guru',
            'guru' => $this->guru,
            'waliKelas' => $waliKelas,
            'jadwalHariIni' => $jadwalHariIni,
            'kelasCount' => $kelasCount,
            'semester' => $this->semester,
        ];
        return view('guru/dashboard', $data);
    }

    public function jadwal()
    {
        $jadwalModel = new JadwalModel();
        $jadwal = [];
        if ($this->semester) {
            $jadwal = $jadwalModel->getJadwalGuru($this->guru['id'], $this->semester['id']);
        }

        $data = [
            'title' => 'Jadwal Mengajar',
            'jadwal' => $jadwal,
            'semester' => $this->semester,
        ];
        return view('guru/jadwal', $data);
    }

    public function absensi()
    {
        $jadwalModel = new JadwalModel();

        // Get kelas/rombel yang diajar guru ini
        $kelasIds = $jadwalModel->select('kelas_id')
            ->distinct()
            ->where('guru_id', $this->guru['id'])
            ->where('semester_id', $this->semester['id'] ?? 0)
            ->where('kelas_id IS NOT NULL')
            ->findAll();

        $kelasModel = new KelasModel();
        $kelasList = [];
        foreach ($kelasIds as $k) {
            if ($k['kelas_id']) {
                $kelas = $kelasModel->find($k['kelas_id']);
                if ($kelas) $kelasList[] = $kelas;
            }
        }

        $data = [
            'title' => 'Input Absensi',
            'kelasList' => $kelasList,
            'semester' => $this->semester,
        ];
        return view('guru/absensi', $data);
    }

    public function inputAbsensi($kelasId)
    {
        $kelasModel = new KelasModel();
        $siswaModel = new SiswaModel();
        $absensiModel = new AbsensiSiswaModel();
        $sessionModel = new \App\Models\AbsensiSessionModel();
        $jadwalModel = new JadwalModel();

        $kelas = $kelasModel->find($kelasId);
        $siswa = $siswaModel->getByKelas($kelasId);
        $tanggal = $this->request->getGet('tanggal') ?? date('Y-m-d');

        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat'];
        $dayOfWeek = date('l', strtotime($tanggal));
        $hari = $hariMap[$dayOfWeek] ?? null;

        $jadwalList = [];
        if ($hari && $this->semester) {
            $rawJadwal = $jadwalModel->select('jadwal.*, mata_pelajaran.nama as mapel_nama')
                ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                ->where('jadwal.kelas_id', $kelasId)
                ->where('jadwal.guru_id', $this->guru['id'])
                ->where('jadwal.semester_id', $this->semester['id'])
                ->where('jadwal.hari', $hari)
                ->orderBy('jadwal.jam_ke')
                ->findAll();

            // Group consecutive entries with same mapel_id
            foreach ($rawJadwal as $j) {
                $last = !empty($jadwalList) ? $jadwalList[count($jadwalList) - 1] : null;
                if ($last && $last['mapel_id'] == $j['mapel_id']) {
                    $jadwalList[count($jadwalList) - 1]['jam_akhir'] = $j['jam_ke'];
                    $jadwalList[count($jadwalList) - 1]['jam_selesai'] = $j['jam_selesai'];
                    $jadwalList[count($jadwalList) - 1]['jadwal_ids'][] = $j['id'];
                } else {
                    $j['jam_awal'] = $j['jam_ke'];
                    $j['jam_akhir'] = $j['jam_ke'];
                    $j['jadwal_ids'] = [$j['id']];
                    $jadwalList[] = $j;
                }
            }
        }

        // Cek session per-kelas (bukan per-jadwal)
        $session = $sessionModel->getActiveSessionForKelas($kelasId, $tanggal);
        $sessionStatus = null;
        if ($session) {
            $expiresWaktu = strtotime($session['expires_at']);
            if (time() <= $expiresWaktu) {
                $sessionStatus = 'open';
            } else {
                $sessionStatus = 'expired';
            }
        }

        // Get existing absensi per jadwal group
        $existingAbsensi = [];
        foreach ($jadwalList as $j) {
            $firstJid = $j['jadwal_ids'][0];
            $existing = $absensiModel->where('jadwal_id', $firstJid)
                ->where('tanggal', $tanggal)
                ->findAll();
            foreach ($existing as $e) {
                $existingAbsensi[$j['id']][$e['siswa_id']] = $e;
            }
        }

        $data = [
            'title' => 'Input Absensi - ' . $kelas['nama_kelas'],
            'kelas' => $kelas,
            'siswa' => $siswa,
            'jadwalList' => $jadwalList,
            'tanggal' => $tanggal,
            'existingAbsensi' => $existingAbsensi,
            'session' => $session,
            'sessionStatus' => $sessionStatus,
        ];
        return view('guru/input_absensi', $data);
    }

    public function openAbsensiSession()
    {
        $kelasId = $this->request->getPost('kelas_id');
        $tanggal = $this->request->getPost('tanggal');
        $durasi = (int)$this->request->getPost('durasi') ?: 540; // default 9 jam

        if (empty($kelasId) || empty($tanggal)) {
            return redirect()->back()->with('error', 'Kelas dan tanggal wajib dipilih.');
        }

        $sessionModel = new \App\Models\AbsensiSessionModel();
        $jadwalModel = new \App\Models\JadwalModel();
        $semesterModel = new \App\Models\SemesterModel();
        $semester = $semesterModel->getAktif();

        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat'];
        $hari = $hariMap[date('l', strtotime($tanggal))] ?? null;

        // Hapus session lama jika ada
        $sessionModel->deleteSessionByKelas($kelasId, $tanggal);

        // Ambil jadwal pertama sebagai referensi jadwal_id
        $jadwal = $jadwalModel->where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->where('semester_id', $semester['id'] ?? 0)
            ->orderBy('jam_ke')
            ->first();

        $now = date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime("+$durasi minutes"));

        $sessionModel->insert([
            'jadwal_id'  => $jadwal['id'] ?? null,
            'guru_id'    => $this->guru['id'],
            'kelas_id'   => $kelasId,
            'rombel_id'  => null,
            'tanggal'    => $tanggal,
            'opened_at'  => $now,
            'expires_at' => $expiresAt,
            'created_at' => $now,
        ]);

        return redirect()->back()->with('success', "Sesi absensi dibuka untuk kelas ini sampai " . date('H:i', strtotime($expiresAt)) . ". Siswa dapat mengisi kehadiran dari akun mereka (sekali klik untuk semua mapel).");
    }

    public function closeAbsensiSession()
    {
        $kelasId = $this->request->getPost('kelas_id');
        $tanggal = $this->request->getPost('tanggal');

        if (empty($kelasId) || empty($tanggal)) {
            return redirect()->back()->with('error', 'Kelas dan tanggal wajib dipilih.');
        }

        $sessionModel = new \App\Models\AbsensiSessionModel();
        $siswaModel = new \App\Models\SiswaModel();
        $absensiModel = new \App\Models\AbsensiSiswaModel();
        $jadwalModel = new \App\Models\JadwalModel();
        $semesterModel = new \App\Models\SemesterModel();
        $semester = $semesterModel->getAktif();

        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat'];
        $hari = $hariMap[date('l', strtotime($tanggal))];

        // Auto-mark Alpha untuk siswa yang belum mengisi
        $siswaList = $siswaModel->getByKelas($kelasId);
        $jadwalList = $jadwalModel->where('kelas_id', $kelasId)
            ->where('hari', $hari)
            ->where('semester_id', $semester['id'] ?? 0)
            ->findAll();

        $alphaCount = 0;
        foreach ($jadwalList as $jadwal) {
            foreach ($siswaList as $siswa) {
                $existing = $absensiModel->where('siswa_id', $siswa['id'])
                    ->where('jadwal_id', $jadwal['id'])
                    ->where('tanggal', $tanggal)
                    ->first();

                if (!$existing) {
                    $absensiModel->insert([
                        'siswa_id'   => $siswa['id'],
                        'kelas_id'   => $kelasId,
                        'rombel_id'  => null,
                        'jadwal_id'  => $jadwal['id'],
                        'tanggal'    => $tanggal,
                        'status'     => 'Alpha',
                        'keterangan' => 'Otomatis alfa (tidak mengisi form)',
                        'guru_id'    => $this->guru['id'],
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    $alphaCount++;
                }
            }
        }

        // Hapus session
        $sessionModel->deleteSessionByKelas($kelasId, $tanggal);

        return redirect()->back()->with('success', "Sesi absensi ditutup. {$alphaCount} siswa yang tidak mengisi ditandai Alpha otomatis.");
    }

    public function storeAbsensi()
    {
        $absensiModel = new AbsensiSiswaModel();
        
        // Now receives array of jadwal_ids since they are grouped
        $jadwalIds = $this->request->getPost('jadwal_ids') ?? [];
        $kelasId   = $this->request->getPost('kelas_id');
        $tanggal   = $this->request->getPost('tanggal');
        $statuses  = $this->request->getPost('status') ?? [];
        $keterangans = $this->request->getPost('keterangan') ?? [];

        // Fallback for single ID if needed
        $singleJadwalId = $this->request->getPost('jadwal_id');
        if (empty($jadwalIds) && $singleJadwalId) {
            $jadwalIds = [$singleJadwalId];
        }

        if (empty($jadwalIds)) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ID Jadwal tidak ditemukan.');
        }

        // ---- Proses Upload Foto Surat (1 foto per siswa per hari) ----
        $uploadPath = FCPATH . 'uploads/surat_absensi/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $fotoSuratFiles = []; // [siswaId => filename]

        foreach ($statuses as $siswaId => $status) {
            if (!in_array($status, ['Sakit', 'Izin'])) {
                continue;
            }

            // Check if existing record already has a photo (no upload needed if keeping old)
            $existingRecord = $absensiModel
                ->where('siswa_id', $siswaId)
                ->where('jadwal_id', $jadwalIds[0])
                ->where('tanggal', $tanggal)
                ->first();

            $file = $this->request->getFile("foto_surat[$siswaId]");

            if ($file && $file->isValid() && !$file->hasMoved()) {
                // Validate type & size
                $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                if (!in_array($file->getMimeType(), $allowedTypes)) {
                    return redirect()->back()->with('error', "File surat untuk siswa ID {$siswaId} harus berupa JPG, PNG, atau PDF.");
                }
                if ($file->getSize() > 2 * 1024 * 1024) {
                    return redirect()->back()->with('error', "Ukuran file surat untuk siswa ID {$siswaId} maksimal 2 MB.");
                }

                $newName = 'surat_' . $siswaId . '_' . $tanggal . '_' . time() . '.' . $file->getExtension();
                $file->move($uploadPath, $newName);
                $fotoSuratFiles[$siswaId] = $newName;

            } elseif (empty($existingRecord['foto_surat'])) {
                // No existing photo and no new upload — wajib
                return redirect()->back()->with('error', "Foto surat sakit/izin wajib diupload untuk siswa dengan status Sakit atau Izin.");
            }
        }

        // ---- Simpan Absensi ----
        $count = 0;
        foreach ($jadwalIds as $jid) {
            foreach ($statuses as $siswaId => $status) {
                $existing = $absensiModel->where('siswa_id', $siswaId)
                    ->where('jadwal_id', $jid)
                    ->where('tanggal', $tanggal)
                    ->first();

                $absData = [
                    'siswa_id'   => $siswaId,
                    'kelas_id'   => $kelasId,
                    'jadwal_id'  => $jid,
                    'tanggal'    => $tanggal,
                    'status'     => $status,
                    'keterangan' => $keterangans[$siswaId] ?? null,
                    'guru_id'    => $this->guru['id'],
                ];

                // Attach photo if uploaded for this student (simpan hanya di jadwal pertama agar tidak duplikat)
                if ($jid == $jadwalIds[0] && isset($fotoSuratFiles[$siswaId])) {
                    $absData['foto_surat'] = $fotoSuratFiles[$siswaId];
                }

                if ($existing) {
                    $absensiModel->update($existing['id'], $absData);
                } else {
                    $absData['created_at'] = date('Y-m-d H:i:s');
                    $absensiModel->insert($absData);
                }
            }
        }
        $count = count($statuses);

        (new ActivityLogModel())->logActivity(session()->get('user_id'), 'input_absensi', 'absensi_siswa', null, ['count' => $count, 'kelas_id' => $kelasId, 'tanggal' => $tanggal]);

        return redirect()->back()->with('success', "Absensi berhasil disimpan untuk $count siswa pada " . count($jadwalIds) . " jam pelajaran sekaligus.");
    }

    public function nilai()
    {
        $jadwalModel = new JadwalModel();

        $kelasIds = $jadwalModel->select('kelas_id, mapel_id')
            ->distinct()
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->where('guru_id', $this->guru['id'])
            ->where('semester_id', $this->semester['id'] ?? 0)
            ->where('kelas_id IS NOT NULL')
            ->findAll();

        $kelasModel = new KelasModel();
        $mapelModel = new MapelModel();
        $assignments = [];
        foreach ($kelasIds as $k) {
            if ($k['kelas_id'] && $k['mapel_id']) {
                $kelas = $kelasModel->find($k['kelas_id']);
                $mapel = $mapelModel->find($k['mapel_id']);
                if ($kelas && $mapel) {
                    $assignments[] = ['kelas' => $kelas, 'mapel' => $mapel];
                }
            }
        }

        $data = [
            'title' => 'Input Nilai',
            'assignments' => $assignments,
        ];
        return view('guru/nilai', $data);
    }

    public function inputNilai($kelasId, $mapelId)
    {
        $kelasModel = new KelasModel();
        $siswaModel = new SiswaModel();
        $mapelModel = new MapelModel();
        $nilaiModel = new NilaiModel();

        $kelas = $kelasModel->find($kelasId);
        $mapel = $mapelModel->find($mapelId);
        $siswa = $siswaModel->getByKelas($kelasId);
        $catatanModel = new \App\Models\CatatanRaporModel();

        $existingNilai = [];
        $lockedStudents = [];
        foreach ($siswa as $s) {
            $cat = $catatanModel->where('siswa_id', $s['id'])->where('semester_id', $this->semester['id'])->first();
            if ($cat && $cat['is_locked']) {
                $lockedStudents[$s['id']] = true;
            }

            $nilai = $nilaiModel->where('siswa_id', $s['id'])
                ->where('mapel_id', $mapelId)
                ->where('semester_id', $this->semester['id'])
                ->findAll();
            foreach ($nilai as $n) {
                $existingNilai[$s['id']][$n['jenis_nilai']] = $n['nilai'];
            }
        }

        $data = [
            'title' => "Input Nilai {$mapel['nama']} - {$kelas['nama_kelas']}",
            'kelas' => $kelas,
            'mapel' => $mapel,
            'siswa' => $siswa,
            'existingNilai' => $existingNilai,
            'lockedStudents' => $lockedStudents,
            'jenisNilai' => ['UH1', 'UH2', 'UH3', 'UTS', 'UAS', 'Tugas', 'Praktik'],
        ];
        return view('guru/input_nilai', $data);
    }

    public function storeNilai()
    {
        $nilaiModel = new NilaiModel();
        $catatanModel = new \App\Models\CatatanRaporModel();
        $mapelId = $this->request->getPost('mapel_id');
        $nilaiData = $this->request->getPost('nilai') ?? [];

        $count = 0;
        foreach ($nilaiData as $siswaId => $jenisArr) {
            $cat = $catatanModel->where('siswa_id', $siswaId)->where('semester_id', $this->semester['id'])->first();
            if ($cat && $cat['is_locked']) {
                continue; // Jangan simpan nilai siswa ini karena rapornya sudah dikunci Wali Kelas
            }

            foreach ($jenisArr as $jenis => $val) {
                if ($val === '' || $val === null) continue;

                $existing = $nilaiModel->where('siswa_id', $siswaId)
                    ->where('mapel_id', $mapelId)
                    ->where('guru_id', $this->guru['id'])
                    ->where('semester_id', $this->semester['id'])
                    ->where('jenis_nilai', $jenis)
                    ->first();

                $nd = [
                    'siswa_id' => $siswaId,
                    'mapel_id' => $mapelId,
                    'guru_id' => $this->guru['id'],
                    'semester_id' => $this->semester['id'],
                    'jenis_nilai' => $jenis,
                    'nilai' => floatval($val),
                ];

                if ($existing) {
                    $nilaiModel->update($existing['id'], $nd);
                } else {
                    $nd['created_at'] = date('Y-m-d H:i:s');
                    $nilaiModel->insert($nd);
                }
                $count++;
            }
        }

        (new ActivityLogModel())->logActivity(session()->get('user_id'), 'input_nilai', 'nilai', null, ['count' => $count]);

        return redirect()->back()->with('success', "$count nilai berhasil disimpan.");
    }

    // ==================== REKAP ABSENSI WALI KELAS ====================

    public function rekapAbsensiWali()
    {
        // 1. Check if the guru is a wali kelas
        $kelasModel = new KelasModel();
        $kelasWali = $kelasModel->where('wali_kelas_id', $this->guru['id'])->first();

        if (!$kelasWali) {
            return redirect()->to('/guru/dashboard')->with('error', 'Anda bukan wali kelas, sehingga tidak memiliki akses ini.');
        }

        $bulan = $this->request->getGet('bulan') ?: date('m');
        $tahun = $this->request->getGet('tahun') ?: date('Y');

        $siswaModel = new SiswaModel();
        $siswaList = $siswaModel->where('kelas_id', $kelasWali['id'])
            ->orderBy('nama', 'ASC')
            ->findAll();

        $rekapData = [];
        $db = \Config\Database::connect();
        $builder = $db->table('absensi_siswa')
            ->select('siswa_id, status, COUNT(DISTINCT tanggal) as total_hari');

        if ($bulan !== 'all') {
            $builder->where('MONTH(tanggal)', $bulan);
        }
        $builder->where('YEAR(tanggal)', $tahun);

        // Filter only students in this class
        $siswaIds = array_column($siswaList, 'id');
        if (!empty($siswaIds)) {
            $builder->whereIn('siswa_id', $siswaIds);
        }

        $rawRekap = $builder->groupBy('siswa_id, status')->get()->getResultArray();

        $formattedRekap = [];
        foreach ($rawRekap as $r) {
            $formattedRekap[$r['siswa_id']][$r['status']] = $r['total_hari'];
        }

        foreach ($siswaList as $s) {
            $rekapData[] = [
                'Siswa' => $s,
                'Hadir' => $formattedRekap[$s['id']]['Hadir'] ?? 0,
                'Sakit' => $formattedRekap[$s['id']]['Sakit'] ?? 0,
                'Izin'  => $formattedRekap[$s['id']]['Izin'] ?? 0,
                'Alpha' => $formattedRekap[$s['id']]['Alpha'] ?? 0,
            ];
        }

        $data = [
            'title' => 'Rekapitulasi Kehadiran Kelas ' . $kelasWali['nama_kelas'],
            'kelasWali' => $kelasWali,
            'rekapData' => $rekapData,
            'selectedBulan' => $bulan,
            'selectedTahun' => $tahun,
            'semester' => $this->semester
        ];

        return view('guru/rekap_absensi', $data);
    }

    // ==================== CATATAN WALI KELAS & EKSKUL ====================

    public function inputCatatanRapor()
    {
        if (!$this->semester) {
            return redirect()->to('/guru/dashboard')->with('error', 'Semester tidak aktif.');
        }

        $kelasModel = new KelasModel();
        $kelasWali = $kelasModel->where('wali_kelas_id', $this->guru['id'])->first();

        if (!$kelasWali) {
            return redirect()->to('/guru/dashboard')->with('error', 'Anda bukan wali kelas, sehingga tidak memiliki akses ke fitur ini.');
        }

        $siswaModel = new SiswaModel();
        $siswaList = $siswaModel->where('kelas_id', $kelasWali['id'])->orderBy('nama', 'ASC')->findAll();

        $ekskulModel = new \App\Models\EkstrakurikulerModel();
        $ekskulList = $ekskulModel->findAll();

        $catatanModel = new \App\Models\CatatanRaporModel();
        $nilaiEkskulModel = new \App\Models\NilaiEkskulModel();

        $existingCatatan = [];
        $existingEkskul = [];

        foreach ($siswaList as $s) {
            $cat = $catatanModel->where('siswa_id', $s['id'])->where('semester_id', $this->semester['id'])->first();
            if ($cat) $existingCatatan[$s['id']] = $cat;

            $eks = $nilaiEkskulModel->where('siswa_id', $s['id'])->where('semester_id', $this->semester['id'])->findAll();
            $existingEkskul[$s['id']] = $eks;
        }

        $data = [
            'title' => 'Input Catatan Rapor & Ekskul',
            'kelasWali' => $kelasWali,
            'siswaList' => $siswaList,
            'ekskulList' => $ekskulList,
            'existingCatatan' => $existingCatatan,
            'existingEkskul' => $existingEkskul,
            'semester' => $this->semester
        ];

        return view('guru/input_catatan_rapor', $data);
    }

    public function storeCatatanRapor()
    {
        if (!$this->semester) {
            return redirect()->back()->with('error', 'Semester tidak aktif.');
        }

        $catatanModel = new \App\Models\CatatanRaporModel();
        $nilaiEkskulModel = new \App\Models\NilaiEkskulModel();

        $siswaIds = $this->request->getPost('siswa_ids') ?? [];
        $catatanWali = $this->request->getPost('catatan_wali') ?? [];
        $sakit = $this->request->getPost('sakit') ?? [];
        $izin = $this->request->getPost('izin') ?? [];
        $alpa = $this->request->getPost('alpa') ?? [];

        $ekskulId1 = $this->request->getPost('ekskul_1') ?? [];
        $nilaiEkskul1 = $this->request->getPost('nilai_ekskul_1') ?? [];
        
        $ekskulId2 = $this->request->getPost('ekskul_2') ?? [];
        $nilaiEkskul2 = $this->request->getPost('nilai_ekskul_2') ?? [];

        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($siswaIds as $sId) {
            // ---- CATATAN & ABSENSI ----
            $catRecord = $catatanModel->where('siswa_id', $sId)->where('semester_id', $this->semester['id'])->first();
            
            // Cek kunci rapor
            if ($catRecord && $catRecord['is_locked']) {
                continue; // lewati karena sudah dikunci/validasi final
            }

            $catData = [
                'siswa_id' => $sId,
                'semester_id' => $this->semester['id'],
                'sakit' => (int)($sakit[$sId] ?? 0),
                'izin' => (int)($izin[$sId] ?? 0),
                'alpa' => (int)($alpa[$sId] ?? 0),
                'catatan_wali' => $catatanWali[$sId] ?? null,
            ];

            if ($catRecord) {
                $catatanModel->update($catRecord['id'], $catData);
            } else {
                $catatanModel->insert($catData);
            }

            // ---- EKSTRAKURIKULER ----
            // Hapus ekskul semester ini untuk insert ulang (replace)
            $nilaiEkskulModel->where('siswa_id', $sId)->where('semester_id', $this->semester['id'])->delete();

            if (!empty($ekskulId1[$sId]) && !empty($nilaiEkskul1[$sId])) {
                $nilaiEkskulModel->insert([
                    'siswa_id' => $sId,
                    'semester_id' => $this->semester['id'],
                    'ekskul_id' => $ekskulId1[$sId],
                    'nilai' => $nilaiEkskul1[$sId]
                ]);
            }

            if (!empty($ekskulId2[$sId]) && !empty($nilaiEkskul2[$sId]) && $ekskulId2[$sId] != $ekskulId1[$sId]) {
                $nilaiEkskulModel->insert([
                    'siswa_id' => $sId,
                    'semester_id' => $this->semester['id'],
                    'ekskul_id' => $ekskulId2[$sId],
                    'nilai' => $nilaiEkskul2[$sId]
                ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal menyimpan data rapor.');
        }

        (new ActivityLogModel())->logActivity(session()->get('user_id'), 'input_catatan_rapor', 'catatan_rapor', null, ['count' => count($siswaIds)]);

        return redirect()->back()->with('success', count($siswaIds).' data rekap rapor berhasil disimpan.');
    }

    public function kunciRapor()
    {
        if (!$this->semester) {
            return redirect()->back()->with('error', 'Semester tidak aktif.');
        }

        $kelasModel = new KelasModel();
        $kelasWali = $kelasModel->where('wali_kelas_id', $this->guru['id'])->first();

        if (!$kelasWali) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda bukan Wali Kelas.');
        }

        $siswaModel = new SiswaModel();
        $siswaList = $siswaModel->where('kelas_id', $kelasWali['id'])->findAll();
        $siswaIds = array_column($siswaList, 'id');

        if (empty($siswaIds)) {
            return redirect()->back()->with('error', 'Tidak ada siswa di kelas ini.');
        }
        
        $db = \Config\Database::connect();
        $db->table('catatan_rapor')
           ->whereIn('siswa_id', $siswaIds)
           ->where('semester_id', $this->semester['id'])
           ->update(['is_locked' => 1]);

        (new ActivityLogModel())->logActivity(session()->get('user_id'), 'kunci_rapor', 'catatan_rapor', null, ['kelas_id' => $kelasWali['id']]);

        return redirect()->back()->with('success', 'Rapor kelas ' . $kelasWali['nama_kelas'] . ' berhasil dikunci. Semua nilai dan catatan sekarang bersifat Final dan tidak dapat diubah lagi oleh Guru Mapel.');
    }

    // ==================== NILAI WALI KELAS ====================

    public function nilaiWali()
    {
        if (!$this->semester) {
            return redirect()->to('/guru/dashboard')->with('error', 'Semester tidak aktif.');
        }

        $kelasModel = new KelasModel();
        $kelasWali  = $kelasModel->where('wali_kelas_id', $this->guru['id'])->first();

        if (!$kelasWali) {
            return redirect()->to('/guru/dashboard')->with('error', 'Anda bukan wali kelas, tidak memiliki akses ke fitur ini.');
        }

        $siswaModel = new SiswaModel();
        $siswaList  = $siswaModel->where('kelas_id', $kelasWali['id'])->orderBy('nama', 'ASC')->findAll();

        $nilaiModel = new NilaiModel();
        $mapelModel = new MapelModel();

        // Get all mapel yang ada nilainya di kelas ini dan semester ini
        $db = \Config\Database::connect();
        $mapelIds = $db->table('nilai')
            ->select('DISTINCT mapel_id')
            ->where('semester_id', $this->semester['id'])
            ->whereIn('siswa_id', array_column($siswaList, 'id'))
            ->get()->getResultArray();

        $mapelList = [];
        foreach ($mapelIds as $m) {
            $mapel = $mapelModel->find($m['mapel_id']);
            if ($mapel) $mapelList[] = $mapel;
        }
        // Sort mapel by nama
        usort($mapelList, fn($a, $b) => strcmp($a['nama'], $b['nama']));

        // Build nilaiData[siswaId][mapelId] = ['values', 'rata_uh', 'nilai_akhir', 'predikat']
        $nilaiData = [];
        $jenisNilai = ['UH1', 'UH2', 'UH3', 'UTS', 'UAS', 'Tugas', 'Praktik'];

        foreach ($siswaList as $s) {
            foreach ($mapelList as $mapel) {
                $nilaiRows = $nilaiModel
                    ->where('siswa_id', $s['id'])
                    ->where('mapel_id', $mapel['id'])
                    ->where('semester_id', $this->semester['id'])
                    ->findAll();

                if (empty($nilaiRows)) {
                    $nilaiData[$s['id']][$mapel['id']] = null;
                    continue;
                }

                $vals = [];
                foreach ($nilaiRows as $n) {
                    $vals[$n['jenis_nilai']] = $n['nilai'];
                }

                $uhVals  = array_filter([$vals['UH1'] ?? null, $vals['UH2'] ?? null, $vals['UH3'] ?? null]);
                $rataUh  = count($uhVals) > 0 ? round(array_sum($uhVals) / count($uhVals), 2) : 0;

                $bUh     = $mapel['bobot_uh']     ?? 50;
                $bTugas  = $mapel['bobot_tugas']  ?? 0;
                $bPrak   = $mapel['bobot_praktik'] ?? 0;
                $bUts    = $mapel['bobot_uts']    ?? 25;
                $bUas    = $mapel['bobot_uas']    ?? 25;

                $na = round(
                    ($rataUh             * ($bUh    / 100)) +
                    (($vals['Tugas']  ?? 0) * ($bTugas / 100)) +
                    (($vals['Praktik']?? 0) * ($bPrak  / 100)) +
                    (($vals['UTS']   ?? 0) * ($bUts   / 100)) +
                    (($vals['UAS']   ?? 0) * ($bUas   / 100)),
                2);

                $predikat = $na >= 88 ? 'A' : ($na >= 75 ? 'B' : ($na >= 62 ? 'C' : 'D'));

                $nilaiData[$s['id']][$mapel['id']] = [
                    'values'      => $vals,
                    'rata_uh'     => $rataUh,
                    'nilai_akhir' => $na,
                    'predikat'    => $predikat,
                ];
            }
        }

        return view('guru/nilai_wali', [
            'title'      => 'Nilai Siswa — Kelas ' . $kelasWali['nama_kelas'],
            'kelasWali'  => $kelasWali,
            'siswaList'  => $siswaList,
            'mapelList'  => $mapelList,
            'nilaiData'  => $nilaiData,
            'jenisNilai' => $jenisNilai,
            'semester'   => $this->semester,
        ]);
    }
}
