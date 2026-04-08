<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\{GuruModel, SiswaModel, KelasModel, MapelModel, UserModel, JadwalModel, SemesterModel, TahunAjaranModel, LaporanKehadiranGuruModel};

class AdminController extends BaseController
{
    public function index()
    {
        $guruModel = new GuruModel();
        $siswaModel = new SiswaModel();
        $kelasModel = new KelasModel();
        $mapelModel = new MapelModel();
        $userModel = new UserModel();

        $data = [
            'title' => 'Dashboard Admin',
            'totalGuru' => $guruModel->countAll(),
            'totalSiswa' => $siswaModel->where('status', 'aktif')->countAllResults(),
            'totalKelas' => $kelasModel->countAll(),
            'totalMapel' => $mapelModel->countAll(),
            'totalUsers' => $userModel->where('is_active', 1)->countAllResults(),
            'recentLogin' => $userModel->where('last_login IS NOT NULL')
                ->orderBy('last_login', 'DESC')
                ->limit(10)
                ->findAll(),
            'kelasList' => $kelasModel->getWithWali(),
        ];

        return view('admin/dashboard', $data);
    }

    public function guru()
    {
        $guruModel = new GuruModel();
        $gurus = $guruModel->orderBy('nama', 'ASC')->findAll();

        $jadwalModel = new JadwalModel();
        $mapelModel = new MapelModel();
        $semesterModel = new SemesterModel();
        $semester = $semesterModel->getAktif();

        if ($semester) {
            $db = \Config\Database::connect();
            foreach ($gurus as &$g) {
                // Source 1: From jadwal (schedule-based)
                $jadwalMapels = $jadwalModel->select('mata_pelajaran.id, mata_pelajaran.nama')
                    ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
                    ->where('jadwal.guru_id', $g['id'])
                    ->where('jadwal.semester_id', $semester['id'])
                    ->distinct()
                    ->findAll();
                
                // Source 2: From guru_mapel_kelas (manually assigned)
                $gmkMapels = $db->table('guru_mapel_kelas gmk')
                    ->join('mata_pelajaran mp', 'mp.id = gmk.mapel_id')
                    ->select('mp.id, mp.nama')
                    ->where('gmk.guru_id', $g['id'])
                    ->where('gmk.semester_id', $semester['id'])
                    ->groupBy('mp.id')
                    ->get()->getResultArray();

                // Merge both sources (unique by id)
                $allMapels = [];
                foreach (array_merge($jadwalMapels, $gmkMapels) as $m) {
                    $allMapels[$m['id']] = $m['nama'];
                }

                $g['mapel_diampu'] = array_values($allMapels);
                $g['mapel_ids'] = array_map('intval', array_keys($allMapels));
            }
        }

        $data = [
            'title' => 'Data Guru',
            'guru' => $gurus,
            'mapelList' => $mapelModel->orderBy('nama')->findAll(),
        ];
        return view('admin/guru', $data);
    }

    public function siswa()
    {
        $kelasId = $this->request->getGet('kelas_id');
        $status  = $this->request->getGet('status') ?: 'aktif';
        $siswaModel = new SiswaModel();
        $kelasModel = new KelasModel();

        $builder = $siswaModel->select('siswa.*, kelas.nama_kelas')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left');

        if ($status !== 'semua') {
            $builder->where('siswa.status', $status);
        }

        if ($kelasId) {
            $builder->where('siswa.kelas_id', $kelasId);
        }

        // Count per status using raw db to avoid query builder state issues
        $db = \Config\Database::connect();
        $statusCounts = [
            'aktif'  => $db->table('siswa')->where('status', 'aktif')->countAllResults(),
            'lulus'  => $db->table('siswa')->where('status', 'lulus')->countAllResults(),
            'pindah' => $db->table('siswa')->where('status', 'pindah')->countAllResults(),
            'keluar' => $db->table('siswa')->where('status', 'keluar')->countAllResults(),
        ];

        $data = [
            'title'         => 'Data Siswa',
            'siswa'         => $builder->orderBy('kelas.nama_kelas')->orderBy('siswa.nama')->findAll(),
            'kelasList'     => $kelasModel->orderBy('tingkat')->orderBy('nama_kelas')->findAll(),
            'selectedKelas' => $kelasId,
            'selectedStatus' => $status,
            'statusCounts'  => $statusCounts,
        ];
        return view('admin/siswa', $data);
    }

    public function bulkUpdateStatusSiswa()
    {
        $siswaIds = $this->request->getPost('siswa_ids') ?? [];
        $status = $this->request->getPost('bulk_status');

        if (empty($siswaIds) || empty($status)) {
            return redirect()->back()->with('error', 'Pilih minimal satu siswa dan status tujuan.');
        }

        $siswaModel = new SiswaModel();
        
        // Cek validasi enum
        if (!in_array($status, ['aktif', 'lulus', 'pindah', 'keluar'])) {
            return redirect()->back()->with('error', 'Status tidak valid.');
        }

        $count = 0;
        foreach ($siswaIds as $id) {
            $siswaModel->update($id, ['status' => $status]);
            $count++;
        }

        return redirect()->back()->with('success', "$count siswa berhasil diubah statusnya menjadi " . ucfirst($status) . ".");
    }

    public function kelas()
    {
        $kelasModel = new KelasModel();
        $siswaModel = new SiswaModel();

        $kelasList = $kelasModel->getWithWali();
        foreach ($kelasList as &$k) {
            $k['jumlah_siswa'] = $siswaModel->where('kelas_id', $k['id'])->where('status', 'aktif')->countAllResults();
        }

        $data = [
            'title' => 'Data Kelas',
            'kelasList' => $kelasList,
        ];
        return view('admin/kelas', $data);
    }

    public function mapel()
    {
        $mapelModel = new MapelModel();
        $data = [
            'title' => 'Mata Pelajaran',
            'mapelList' => $mapelModel->orderBy('kelompok')->orderBy('nama')->findAll(),
        ];
        return view('admin/mapel', $data);
    }

    public function jadwal()
    {
        $semesterModel = new SemesterModel();
        $semester = $semesterModel->getAktif();

        $jadwalModel = new JadwalModel();
        $kelasModel = new KelasModel();
        $guruModel = new GuruModel();
        $mapelModel = new MapelModel();

        $kelasFilter = $this->request->getGet('kelas_id');

        $builder = $jadwalModel->select('jadwal.*, mata_pelajaran.nama as mapel_nama, guru.nama as guru_nama, kelas.nama_kelas, rombel.nama_rombel')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('guru', 'guru.id = jadwal.guru_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id', 'left')
            ->join('rombel', 'rombel.id = jadwal.rombel_id', 'left')
            ->where('jadwal.semester_id', $semester['id']);

        if ($kelasFilter) {
            $builder->where('jadwal.kelas_id', $kelasFilter);
        }

        $data = [
            'title' => 'Jadwal Pelajaran',
            'jadwalList' => $builder->orderBy('FIELD(jadwal.hari, "Senin", "Selasa", "Rabu", "Kamis", "Jumat")')->orderBy('jadwal.jam_ke')->findAll(),
            'kelasList' => $kelasModel->orderBy('tingkat')->orderBy('nama_kelas')->findAll(),
            'guruList' => $guruModel->orderBy('nama')->findAll(),
            'mapelList' => $mapelModel->orderBy('nama')->findAll(),
            'semester' => $semester,
            'selectedKelas' => $kelasFilter,
        ];
        return view('admin/jadwal', $data);
    }

    public function storeJadwal()
    {
        $jadwalModel = new JadwalModel();
        $semesterModel = new SemesterModel();
        $semester = $semesterModel->getAktif();

        $data = [
            'kelas_id' => $this->request->getPost('kelas_id') ?: null,
            'rombel_id' => $this->request->getPost('rombel_id') ?: null,
            'mapel_id' => $this->request->getPost('mapel_id'),
            'guru_id' => $this->request->getPost('guru_id'),
            'semester_id' => $semester['id'],
            'hari' => $this->request->getPost('hari'),
            'jam_ke' => $this->request->getPost('jam_ke'),
            'jam_mulai' => $this->request->getPost('jam_mulai'),
            'jam_selesai' => $this->request->getPost('jam_selesai'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        // Check conflict
        $conflict = $jadwalModel->where('guru_id', $data['guru_id'])
            ->where('hari', $data['hari'])
            ->where('jam_ke', $data['jam_ke'])
            ->where('semester_id', $data['semester_id'])
            ->first();

        if ($conflict) {
            return redirect()->back()->with('error', 'Konflik jadwal: Guru sudah mengajar di slot yang sama!');
        }

        $jadwalModel->insert($data);
        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function deleteJadwal($id)
    {
        (new JadwalModel())->delete($id);
        return redirect()->to('/admin/jadwal')->with('success', 'Jadwal berhasil dihapus.');
    }

    public function monitoring()
    {
        $db = \Config\Database::connect();
        $tanggal = $this->request->getGet('tanggal') ?: date('Y-m-d');

        // Get all laporan for this date, grouped by guru+mapel+kelas+status
        // Count DISTINCT siswa_id to avoid inflated counts from multi-jam groups
        $rawLaporan = $db->table('laporan_kehadiran_guru')
            ->select('guru_id, mapel_id, kelas_id, status, COUNT(DISTINCT siswa_id) as jumlah')
            ->where('tanggal', $tanggal)
            ->groupBy('guru_id, mapel_id, kelas_id, status')
            ->get()->getResultArray();

        // Build per-kelas aggregation
        $entries = [];
        foreach ($rawLaporan as $r) {
            $key = $r['guru_id'] . '-' . $r['mapel_id'] . '-' . $r['kelas_id'];
            if (!isset($entries[$key])) {
                $entries[$key] = [
                    'guru_id' => $r['guru_id'], 'mapel_id' => $r['mapel_id'], 'kelas_id' => $r['kelas_id'],
                    'hadir' => 0, 'tugas' => 0, 'tidak_hadir' => 0, 'total_jawab' => 0,
                ];
            }
            $entries[$key][$r['status']] = (int)$r['jumlah'];
            $entries[$key]['total_jawab'] += (int)$r['jumlah'];
        }

        // Enrich with names and student counts
        $guruModel  = new GuruModel();
        $mapelModel = new MapelModel();
        $kelasModel = new KelasModel();

        foreach ($entries as $key => &$e) {
            $guru  = $guruModel->find($e['guru_id']);
            $mapel = $mapelModel->find($e['mapel_id']);
            $kelas = $kelasModel->find($e['kelas_id']);

            $e['guru_nama']  = $guru['nama'] ?? '-';
            $e['mapel_nama'] = $mapel['nama'] ?? '-';
            $e['nama_kelas'] = $kelas['nama_kelas'] ?? '-';

            $e['total_siswa'] = $db->table('siswa')->where('kelas_id', $e['kelas_id'])->where('status', 'aktif')->countAllResults();
            $e['belum'] = max(0, $e['total_siswa'] - $e['total_jawab']);

            // Consensus from majority
            $votes = ['hadir' => $e['hadir'], 'tugas' => $e['tugas'], 'tidak_hadir' => $e['tidak_hadir']];
            arsort($votes);
            $e['kesimpulan'] = array_key_first($votes);

            // Verified count
            $e['verified_count'] = $db->table('laporan_kehadiran_guru')
                ->where(['guru_id' => $e['guru_id'], 'mapel_id' => $e['mapel_id'], 'kelas_id' => $e['kelas_id'], 'tanggal' => $tanggal, 'is_verified' => 1])
                ->countAllResults();
        }
        unset($e);

        usort($entries, fn($a, $b) => strcmp($a['nama_kelas'] . $a['mapel_nama'], $b['nama_kelas'] . $b['mapel_nama']));

        $stats = [
            'total_entries' => count($entries),
            'total_laporan' => (int)($db->query("SELECT COUNT(DISTINCT siswa_id) as cnt FROM laporan_kehadiran_guru WHERE tanggal = ?", [$tanggal])->getRow()->cnt ?? 0),
            'verified'      => $db->table('laporan_kehadiran_guru')->where('tanggal', $tanggal)->where('is_verified', 1)->countAllResults(),
        ];

        $availDates = $db->table('laporan_kehadiran_guru')
            ->select('tanggal, COUNT(*) as jumlah')->groupBy('tanggal')->orderBy('tanggal', 'DESC')->limit(30)
            ->get()->getResultArray();

        return view('admin/monitoring', [
            'title' => 'Monitoring Kehadiran Guru', 'entries' => array_values($entries),
            'tanggal' => $tanggal, 'stats' => $stats, 'availDates' => $availDates,
        ]);
    }

    public function verifyLaporan($id)
    {
        $laporanModel = new LaporanKehadiranGuruModel();
        $status = $this->request->getPost('status');

        $laporanModel->update($id, [
            'is_verified' => $status,
            'verified_by' => session()->get('user_id'),
        ]);

        return redirect()->back()->with('success', 'Status verifikasi diperbarui.');
    }

    // ==========================================
    // CRUD GURU
    // ==========================================

    public function storeGuru()
    {
        $guruModel = new GuruModel();
        $userModel = new UserModel();

        $guru = [
            'nuptk'         => $this->request->getPost('nuptk'),
            'nama'          => $this->request->getPost('nama'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        $guruModel->insert($guru);
        $guruId = $guruModel->getInsertID();

        // Create user account
        $userModel->insert([
            'username'       => $guru['nuptk'],
            'password'       => password_hash('123123', PASSWORD_DEFAULT),
            'role'           => 'guru',
            'entity_id'      => $guruId,
            'is_active'      => 1,
            'is_first_login' => 1,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        // Save mapel associations
        $mapelIds = $this->request->getPost('mapel_ids') ?? [];
        if (!empty($mapelIds)) {
            $semester = (new SemesterModel())->getAktif();
            if ($semester) {
                $db = \Config\Database::connect();
                foreach ($mapelIds as $mapelId) {
                    $db->table('guru_mapel_kelas')->insert([
                        'guru_id'     => $guruId,
                        'mapel_id'    => $mapelId,
                        'semester_id' => $semester['id'],
                    ]);
                }
            }
        }

        return redirect()->to('/admin/guru')->with('success', "Guru {$guru['nama']} berhasil ditambahkan.");
    }

    public function updateGuru($id)
    {
        $guruModel = new GuruModel();
        $userModel = new UserModel();

        $guru = $guruModel->find($id);
        if (!$guru) return redirect()->to('/admin/guru')->with('error', 'Guru tidak ditemukan.');

        $newNuptk = $this->request->getPost('nuptk');
        $guruModel->update($id, [
            'nuptk'         => $newNuptk,
            'nama'          => $this->request->getPost('nama'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
        ]);

        // Update username if NUPTK changed
        if ($guru['nuptk'] !== $newNuptk) {
            $user = $userModel->where('entity_id', $id)->where('role', 'guru')->first();
            if ($user) $userModel->update($user['id'], ['username' => $newNuptk]);
        }

        // Update mapel associations
        $mapelIds = $this->request->getPost('mapel_ids') ?? [];
        $semester = (new SemesterModel())->getAktif();
        if ($semester) {
            $db = \Config\Database::connect();
            // Remove old associations for this guru in the active semester
            $db->table('guru_mapel_kelas')
               ->where('guru_id', $id)
               ->where('semester_id', $semester['id'])
               ->delete();
            
            // Insert new ones
            foreach ($mapelIds as $mapelId) {
                $db->table('guru_mapel_kelas')->insert([
                    'guru_id'     => $id,
                    'mapel_id'    => $mapelId,
                    'semester_id' => $semester['id'],
                ]);
            }
        }

        return redirect()->to('/admin/guru')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function deleteGuru($id)
    {
        $guruModel = new GuruModel();
        $userModel = new UserModel();

        // Delete user account
        $userModel->where('entity_id', $id)->where('role', 'guru')->delete();
        $guruModel->delete($id);

        return redirect()->to('/admin/guru')->with('success', 'Data guru berhasil dihapus.');
    }

    // ==========================================
    // CRUD SISWA
    // ==========================================

    public function storeSiswa()
    {
        $siswaModel = new SiswaModel();
        $userModel = new UserModel();

        $siswa = [
            'username'      => $this->request->getPost('username'),
            'nisn'          => $this->request->getPost('nisn'),
            'nis'           => $this->request->getPost('nis'),
            'nama'          => $this->request->getPost('nama'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'  => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'nama_ortu'     => $this->request->getPost('nama_ortu'),
            'no_hp_ortu'    => $this->request->getPost('no_hp_ortu'),
            'kelas_id'      => $this->request->getPost('kelas_id') ?: null,
            'tahun_masuk'   => $this->request->getPost('tahun_masuk') ?: date('Y'),
            'status'        => 'aktif',
            'is_monitoring' => 0,
            'sumber_data'   => 'manual',
            'created_at'    => date('Y-m-d H:i:s'),
        ];

        $siswaModel->insert($siswa);
        $siswaId = $siswaModel->getInsertID();

        $userModel->insert([
            'username'       => $siswa['username'],
            'password'       => password_hash('123123', PASSWORD_DEFAULT),
            'role'           => 'siswa',
            'entity_id'      => $siswaId,
            'is_active'      => 1,
            'is_first_login' => 1,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/siswa')->with('success', "Siswa {$siswa['nama']} berhasil ditambahkan.");
    }

    public function updateSiswa($id)
    {
        $siswaModel = new SiswaModel();
        $userModel = new UserModel();

        $siswa = $siswaModel->find($id);
        if (!$siswa) return redirect()->to('/admin/siswa')->with('error', 'Siswa tidak ditemukan.');

        $newUsername = $this->request->getPost('username');
        $siswaModel->update($id, [
            'username'      => $newUsername,
            'nisn'          => $this->request->getPost('nisn'),
            'nis'           => $this->request->getPost('nis'),
            'nama'          => $this->request->getPost('nama'),
            'jenis_kelamin' => $this->request->getPost('jenis_kelamin'),
            'tempat_lahir'  => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir') ?: null,
            'nama_ortu'     => $this->request->getPost('nama_ortu'),
            'no_hp_ortu'    => $this->request->getPost('no_hp_ortu'),
            'kelas_id'      => $this->request->getPost('kelas_id') ?: null,
            'status'        => $this->request->getPost('status') ?: 'aktif',
        ]);

        if ($siswa['username'] !== $newUsername) {
            $user = $userModel->where('entity_id', $id)->where('role', 'siswa')->first();
            if ($user) $userModel->update($user['id'], ['username' => $newUsername]);
        }

        return redirect()->to('/admin/siswa')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function deleteSiswa($id)
    {
        $siswaModel = new SiswaModel();
        $userModel = new UserModel();

        $userModel->where('entity_id', $id)->where('role', 'siswa')->delete();
        $siswaModel->delete($id);

        return redirect()->to('/admin/siswa')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function toggleMonitoring($id)
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->find($id);

        if (!$siswa) {
            return redirect()->to('/admin/siswa')->with('error', 'Siswa tidak ditemukan.');
        }

        $newStatus = $siswa['is_monitoring'] ? 0 : 1;
        $siswaModel->update($id, ['is_monitoring' => $newStatus]);

        $label = $newStatus ? 'ditunjuk untuk monitoring guru' : 'dilepas dari monitoring guru';
        return redirect()->to('/admin/siswa')->with('success', "Siswa {$siswa['nama']} berhasil {$label}.");
    }

    // ==========================================
    // CRUD KELAS
    // ==========================================

    public function storeKelas()
    {
        $kelasModel = new KelasModel();
        $taModel = new TahunAjaranModel();
        $ta = $taModel->where('is_aktif', 1)->first();

        $kelasModel->insert([
            'nama_kelas'      => $this->request->getPost('nama_kelas'),
            'tingkat'         => $this->request->getPost('tingkat'),
            'jurusan'         => $this->request->getPost('jurusan') ?: null,
            'wali_kelas_id'   => $this->request->getPost('wali_kelas_id') ?: null,
            'tahun_ajaran_id' => $ta['id'],
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function updateKelas($id)
    {
        $kelasModel = new KelasModel();

        $kelasModel->update($id, [
            'nama_kelas'    => $this->request->getPost('nama_kelas'),
            'tingkat'       => $this->request->getPost('tingkat'),
            'jurusan'       => $this->request->getPost('jurusan') ?: null,
            'wali_kelas_id' => $this->request->getPost('wali_kelas_id') ?: null,
        ]);

        return redirect()->to('/admin/kelas')->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function deleteKelas($id)
    {
        (new KelasModel())->delete($id);
        return redirect()->to('/admin/kelas')->with('success', 'Kelas berhasil dihapus.');
    }

    // ==========================================
    // CRUD MAPEL
    // ==========================================

    public function storeMapel()
    {
        $mapelModel = new MapelModel();

        $weights = [
            'bobot_uh'      => (int)$this->request->getPost('bobot_uh') ?: 50,
            'bobot_tugas'   => (int)$this->request->getPost('bobot_tugas') ?: 0,
            'bobot_praktik' => (int)$this->request->getPost('bobot_praktik') ?: 0,
            'bobot_uts'     => (int)$this->request->getPost('bobot_uts') ?: 25,
            'bobot_uas'     => (int)$this->request->getPost('bobot_uas') ?: 25,
        ];

        if (array_sum($weights) !== 100) {
            return redirect()->back()->with('error', 'Gagal: Total persentase bobot nilai harus pas 100%.');
        }

        $mapelModel->insert(array_merge([
            'kode'       => $this->request->getPost('kode'),
            'nama'       => $this->request->getPost('nama'),
            'kelompok'   => $this->request->getPost('kelompok'),
            'created_at' => date('Y-m-d H:i:s'),
        ], $weights));

        return redirect()->to('/admin/mapel')->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function updateMapel($id)
    {
        $weights = [
            'bobot_uh'      => (int)$this->request->getPost('bobot_uh') ?: 50,
            'bobot_tugas'   => (int)$this->request->getPost('bobot_tugas') ?: 0,
            'bobot_praktik' => (int)$this->request->getPost('bobot_praktik') ?: 0,
            'bobot_uts'     => (int)$this->request->getPost('bobot_uts') ?: 25,
            'bobot_uas'     => (int)$this->request->getPost('bobot_uas') ?: 25,
        ];

        if (array_sum($weights) !== 100) {
            return redirect()->back()->with('error', 'Gagal memperbarui: Total persentase bobot nilai harus pas 100%.');
        }

        (new MapelModel())->update($id, array_merge([
            'kode'     => $this->request->getPost('kode'),
            'nama'     => $this->request->getPost('nama'),
            'kelompok' => $this->request->getPost('kelompok'),
        ], $weights));

        return redirect()->to('/admin/mapel')->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function deleteMapel($id)
    {
        (new MapelModel())->delete($id);
        return redirect()->to('/admin/mapel')->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    // ==========================================
    // RAPOR
    // ==========================================

    public function rapor()
    {
        $kelasId = $this->request->getGet('kelas_id');
        $siswaModel = new SiswaModel();
        $kelasModel = new KelasModel();

        $data = [
            'title'     => 'Rapor Siswa',
            'kelasList' => $kelasModel->orderBy('tingkat')->orderBy('nama_kelas')->findAll(),
            'selectedKelas' => $kelasId,
            'siswa'     => $kelasId ? $siswaModel->getByKelas($kelasId) : [],
        ];
        return view('admin/rapor', $data);
    }

    public function viewRapor($siswaId)
    {
        $siswaModel = new SiswaModel();
        $semesterModel = new SemesterModel();
        $nilaiModel = new \App\Models\NilaiModel();
        $mapelModel = new MapelModel();

        $siswa = $siswaModel->getWithKelas($siswaId);
        $semester = $semesterModel->getAktif();

        if (!$siswa || !$semester) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        // Get all nilai for this student
        $nilai = $nilaiModel->select('nilai.*, mata_pelajaran.nama as mapel_nama, mata_pelajaran.kode as mapel_kode, mata_pelajaran.kelompok, mata_pelajaran.bobot_uh, mata_pelajaran.bobot_tugas, mata_pelajaran.bobot_praktik, mata_pelajaran.bobot_uts, mata_pelajaran.bobot_uas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = nilai.mapel_id')
            ->where('nilai.siswa_id', $siswaId)
            ->where('nilai.semester_id', $semester['id'])
            ->orderBy('mata_pelajaran.kelompok')
            ->orderBy('mata_pelajaran.nama')
            ->findAll();

        // Group by mapel
        $nilaiPerMapel = [];
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

        // Calculate averages
        foreach ($nilaiPerMapel as $mapel => &$info) {
            $vals = $info['values'];
            $uhVals = array_filter([$vals['UH1'] ?? null, $vals['UH2'] ?? null, $vals['UH3'] ?? null]);
            $info['rata_uh'] = count($uhVals) > 0 ? round(array_sum($uhVals) / count($uhVals), 2) : 0;
            $info['tugas'] = $vals['Tugas'] ?? 0;
            $info['praktik'] = $vals['Praktik'] ?? 0;
            $info['uts'] = $vals['UTS'] ?? 0;
            $info['uas'] = $vals['UAS'] ?? 0;
            
            // Dynamic weighted average
            $info['nilai_akhir'] = round(
                ($info['rata_uh'] * ($info['bobot_uh'] / 100)) + 
                ($info['tugas'] * ($info['bobot_tugas'] / 100)) + 
                ($info['praktik'] * ($info['bobot_praktik'] / 100)) + 
                ($info['uts'] * ($info['bobot_uts'] / 100)) + 
                ($info['uas'] * ($info['bobot_uas'] / 100)),
            2);
            
            // Predikat
            $na = $info['nilai_akhir'];
            $info['predikat'] = $na >= 88 ? 'A' : ($na >= 75 ? 'B' : ($na >= 62 ? 'C' : 'D'));
        }

        $kelasModel = new KelasModel();
        $guruModel = new GuruModel();
        $waliKelas = null;
        if ($siswa['kelas_id']) {
            $kelas = $kelasModel->find($siswa['kelas_id']);
            if ($kelas && $kelas['wali_kelas_id']) {
                $waliKelas = $guruModel->find($kelas['wali_kelas_id']);
            }
        }

        $catatanModel = new \App\Models\CatatanRaporModel();
        $catatan = $catatanModel->where('siswa_id', $siswaId)->where('semester_id', $semester['id'])->first();

        $ekskulModel = new \App\Models\NilaiEkskulModel();
        $ekskul = $ekskulModel->select('nilai_ekskul.*, ekstrakurikuler.nama_ekskul')
            ->join('ekstrakurikuler', 'ekstrakurikuler.id = nilai_ekskul.ekskul_id')
            ->where('siswa_id', $siswaId)
            ->where('semester_id', $semester['id'])
            ->findAll();

        $data = [
            'title'        => "Rapor - {$siswa['nama']}",
            'siswa'        => $siswa,
            'semester'     => $semester,
            'nilaiPerMapel' => $nilaiPerMapel,
            'jenisNilai'   => ['UH1', 'UH2', 'UH3', 'UTS', 'UAS', 'Tugas', 'Praktik'],
            'waliKelas'    => $waliKelas,
            'catatan'      => $catatan,
            'ekskul'       => $ekskul,
            'isPdf'        => false,
        ];
        return view('admin/rapor_detail', $data);
    }

    public function printRapor($siswaId)
    {
        // Same logic as viewRapor but with isPdf = true (for print layout)
        $siswaModel = new SiswaModel();
        $semesterModel = new SemesterModel();
        $nilaiModel = new \App\Models\NilaiModel();
        $mapelModel = new MapelModel();

        $siswa = $siswaModel->getWithKelas($siswaId);
        $semester = $semesterModel->getAktif();

        if (!$siswa || !$semester) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $nilai = $nilaiModel->select('nilai.*, mata_pelajaran.nama as mapel_nama, mata_pelajaran.kode as mapel_kode, mata_pelajaran.kelompok, mata_pelajaran.bobot_uh, mata_pelajaran.bobot_tugas, mata_pelajaran.bobot_praktik, mata_pelajaran.bobot_uts, mata_pelajaran.bobot_uas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = nilai.mapel_id')
            ->where('nilai.siswa_id', $siswaId)
            ->where('nilai.semester_id', $semester['id'])
            ->orderBy('mata_pelajaran.kelompok')
            ->orderBy('mata_pelajaran.nama')
            ->findAll();

        $nilaiPerMapel = [];
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
            
            // Dynamic weighted average
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

        $kelasModel = new KelasModel();
        $guruModel = new GuruModel();
        $waliKelas = null;
        if ($siswa['kelas_id']) {
            $kelas = $kelasModel->find($siswa['kelas_id']);
            if ($kelas && $kelas['wali_kelas_id']) {
                $waliKelas = $guruModel->find($kelas['wali_kelas_id']);
            }
        }

        $catatanModel = new \App\Models\CatatanRaporModel();
        $catatan = $catatanModel->where('siswa_id', $siswaId)->where('semester_id', $semester['id'])->first();

        $ekskulModel = new \App\Models\NilaiEkskulModel();
        $ekskul = $ekskulModel->select('nilai_ekskul.*, ekstrakurikuler.nama_ekskul')
            ->join('ekstrakurikuler', 'ekstrakurikuler.id = nilai_ekskul.ekskul_id')
            ->where('siswa_id', $siswaId)
            ->where('semester_id', $semester['id'])
            ->findAll();

        $data = [
            'title'        => "Rapor - {$siswa['nama']}",
            'siswa'        => $siswa,
            'semester'     => $semester,
            'nilaiPerMapel' => $nilaiPerMapel,
            'waliKelas'    => $waliKelas,
            'catatan'      => $catatan,
            'ekskul'       => $ekskul,
            'isPdf'        => true,
        ];
        return view('admin/rapor_print', $data);
    }

    // ==================== IMPORT SISWA ====================

    public function importSiswa()
    {
        $file = $this->request->getFile('file_excel');
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid. Upload file Excel (.xlsx).');
        }

        $ext = $file->getClientExtension();
        if (!in_array($ext, ['xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format file harus .xlsx atau .xls');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $siswaModel = new SiswaModel();
            $userModel  = new UserModel();
            $kelasModel = new KelasModel();

            // Build kelas lookup
            $allKelas = $kelasModel->findAll();
            $kelasMap = [];
            foreach ($allKelas as $k) {
                $kelasMap[strtoupper(trim($k['nama_kelas']))] = $k['id'];
            }

            $imported = 0;
            $skipped = 0;
            $errors = [];

            for ($row = 2; $row <= $highestRow; $row++) {
                $username = trim($sheet->getCell('A' . $row)->getValue() ?? '');
                $nama     = trim($sheet->getCell('B' . $row)->getValue() ?? '');
                $jk       = strtoupper(trim($sheet->getCell('C' . $row)->getValue() ?? 'L'));
                $kelasNama = strtoupper(trim($sheet->getCell('D' . $row)->getValue() ?? ''));

                if (empty($username) || empty($nama)) continue;

                // Check duplicate
                $existing = $siswaModel->where('username', $username)->first();
                if ($existing) {
                    $skipped++;
                    continue;
                }

                $kelasId = $kelasMap[$kelasNama] ?? null;

                $siswaData = [
                    'username'       => $username,
                    'nama'           => $nama,
                    'jenis_kelamin'  => in_array($jk, ['L', 'P']) ? $jk : 'L',
                    'nisn'           => $username,
                    'kelas_id'       => $kelasId,
                    'tahun_masuk'    => date('Y'),
                    'status'         => 'aktif',
                    'sumber_data'    => 'import',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ];

                $siswaModel->insert($siswaData);
                $siswaId = $siswaModel->getInsertID();

                // Create user account
                $userModel->insert([
                    'username'       => $username,
                    'password'       => password_hash('123123', PASSWORD_DEFAULT),
                    'role'           => 'siswa',
                    'entity_id'      => $siswaId,
                    'is_active'      => 1,
                    'is_first_login' => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);

                $imported++;
            }

            $msg = "{$imported} siswa berhasil diimport.";
            if ($skipped > 0) $msg .= " {$skipped} data dilewati (username sudah ada).";

            return redirect()->to('/admin/siswa')->with('success', $msg);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal import: ' . $e->getMessage());
        }
    }

    public function downloadTemplateSiswa()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import Siswa');

        // Headers - NISN is column A (used as username/login)
        $headers = ['NISN (untuk login)', 'Nama Lengkap', 'JK (L/P)', 'Kelas'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4F46E5');
            $sheet->getStyle($col . '1')->getFont()->getColor()->setRGB('FFFFFF');
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Example data
        $examples = [
            ['0012345678', 'Ahmad Fauzan', 'L', 'X-1'],
            ['0012345679', 'Siti Nurhaliza', 'P', 'X-2'],
            ['0098765432', 'Budi Santoso', 'L', 'XI-1'],
        ];
        foreach ($examples as $i => $row) {
            foreach ($row as $j => $val) {
                $sheet->setCellValue(chr(65 + $j) . ($i + 2), $val);
            }
            $sheet->getStyle('A' . ($i + 2) . ':D' . ($i + 2))->getFont()
                ->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('999999'));
        }

        // Instructions sheet
        $instrSheet = $spreadsheet->createSheet();
        $instrSheet->setTitle('Petunjuk');
        $instructions = [
            ['PETUNJUK PENGISIAN TEMPLATE IMPORT SISWA'],
            [''],
            ['Kolom A: NISN (WAJIB) - Nomor Induk Siswa Nasional, digunakan untuk login'],
            ['Kolom B: Nama Lengkap (WAJIB) - Nama lengkap siswa'],
            ['Kolom C: JK (WAJIB) - L untuk Laki-laki, P untuk Perempuan'],
            ['Kolom D: Kelas (opsional) - Nama kelas sesuai sistem (contoh: X-1, XI-3, XII MIPA-1)'],
            [''],
            ['CATATAN:'],
            ['- Baris pertama adalah header, data dimulai dari baris 2'],
            ['- Hapus contoh data (baris abu-abu) sebelum mengisi data asli'],
            ['- Password default siswa baru: 123123'],
            ['- Siswa dengan No Siswa yang sudah ada akan dilewati'],
        ];
        foreach ($instructions as $i => $row) {
            $instrSheet->setCellValue('A' . ($i + 1), $row[0]);
        }
        $instrSheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $instrSheet->getColumnDimension('A')->setWidth(70);

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="template_import_siswa.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    // ==================== REKAP ABSENSI ====================

    public function rekapAbsensi()
    {
        $kelasId = $this->request->getGet('kelas_id');
        $bulan   = $this->request->getGet('bulan') ?: date('m');
        $tahun   = $this->request->getGet('tahun') ?: date('Y');

        $kelasModel = new KelasModel();
        $siswaModel = new SiswaModel();
        $semesterModel = new SemesterModel();

        $semester = $semesterModel->getAktif();
        $kelasList = $kelasModel->orderBy('tingkat')->orderBy('nama_kelas')->findAll();

        $rekapData = [];
        $selectedKelas = null;

        if ($kelasId) {
            $selectedKelas = $kelasModel->find($kelasId);
            $siswaList = $siswaModel->where('kelas_id', $kelasId)
                                    ->orderBy('nama', 'ASC')
                                    ->findAll();

            $db = \Config\Database::connect();
            $builder = $db->table('absensi_siswa')
                          ->select('siswa_id, status, COUNT(DISTINCT tanggal) as total_hari');
            
            if ($bulan !== 'all') {
                $builder->where('MONTH(tanggal)', $bulan);
            }
            $builder->where('YEAR(tanggal)', $tahun);
            
            // Filter to only students in this class
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
        }

        $data = [
            'title' => 'Rekapitulasi Absensi',
            'kelasList' => $kelasList,
            'rekapData' => $rekapData,
            'selectedKelas' => $kelasId,
            'selectedBulan' => $bulan,
            'selectedTahun' => $tahun,
            'semester' => $semester
        ];

        return view('admin/rekap_absensi', $data);
    }

    // ==========================================
    // DAPODIK SYNC
    // ==========================================

    public function dapodikSync()
    {
        return view('admin/dapodik_sync', ['title' => 'Sinkronisasi Dapodik']);
    }

    public function compareDapodik()
    {
        $input = $this->request->getJSON(true);
        $siswaData = $input['siswa'] ?? [];
        $siswaModel = new SiswaModel();

        $results = [];
        foreach ($siswaData as $s) {
            $nisn = $s['nisn'] ?? ($s['NISN'] ?? null);
            $nama = $s['nama'] ?? ($s['nama_pd'] ?? ($s['nama_siswa'] ?? ''));
            $jk = $s['jenis_kelamin'] ?? ($s['jk'] ?? '');
            if ($jk === 'Laki-laki' || $jk === 'L') $jk = 'L';
            elseif ($jk === 'Perempuan' || $jk === 'P') $jk = 'P';

            $dapodikRow = [
                'nisn'             => $nisn,
                'nik'              => $s['nik'] ?? null,
                'nama'             => $nama,
                'jenis_kelamin'    => $jk,
                'tempat_lahir'     => $s['tempat_lahir'] ?? null,
                'tanggal_lahir'    => $s['tanggal_lahir'] ?? null,
                'agama'            => $s['agama'] ?? ($s['agama_id_str'] ?? null),
                'alamat'           => $s['alamat_jalan'] ?? ($s['alamat'] ?? null),
                'nama_ayah'        => $s['nama_ayah'] ?? null,
                'pekerjaan_ayah'   => $s['pekerjaan_ayah'] ?? null,
                'nama_ibu'         => $s['nama_ibu'] ?? ($s['nama_ibu_kandung'] ?? null),
                'pekerjaan_ibu'    => $s['pekerjaan_ibu'] ?? null,
                'nama_wali'        => $s['nama_wali'] ?? null,
                'pekerjaan_wali'   => $s['pekerjaan_wali'] ?? null,
                'no_hp_ortu'       => $s['no_telepon'] ?? ($s['nomor_telepon_seluler'] ?? null),
                'asal_sekolah'     => $s['sekolah_asal'] ?? null,
                'peserta_didik_id' => $s['peserta_didik_id'] ?? ($s['registrasi_id'] ?? null),
            ];

            // Find existing record by NISN
            $existing = $nisn ? $siswaModel->where('nisn', $nisn)->first() : null;

            if (!$existing) {
                $results[] = array_merge($dapodikRow, [
                    'status' => 'new',
                    'changes' => null,
                    'local_id' => null,
                ]);
            } else {
                $changes = [];
                $fieldsToCompare = ['nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'nik',
                    'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu', 'nama_wali', 'pekerjaan_wali', 'no_hp_ortu', 'asal_sekolah'];
                
                foreach ($fieldsToCompare as $field) {
                    $newVal = $dapodikRow[$field] ?? '';
                    $oldVal = $existing[$field] ?? '';
                    if ($newVal && $newVal !== $oldVal) {
                        $changes[] = ['field' => $field, 'old' => $oldVal, 'new' => $newVal];
                    }
                }

                $results[] = array_merge($dapodikRow, [
                    'status' => count($changes) > 0 ? 'changed' : 'same',
                    'changes' => $changes,
                    'local_id' => $existing['id'],
                ]);
            }
        }

        return $this->response->setJSON(['results' => $results]);
    }

    public function applyDapodik()
    {
        $input = $this->request->getJSON(true);
        $data = $input['data'] ?? [];
        $siswaModel = new SiswaModel();
        $userModel = new UserModel();

        $inserted = 0;
        $updated = 0;

        foreach ($data as $row) {
            $status = $row['status'] ?? 'same';
            if ($status === 'same') continue;

            $saveData = [
                'nisn'             => $row['nisn'] ?? null,
                'nik'              => $row['nik'] ?? null,
                'nama'             => $row['nama'] ?? '',
                'jenis_kelamin'    => $row['jenis_kelamin'] ?? 'L',
                'tempat_lahir'     => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'    => $row['tanggal_lahir'] ?? null,
                'agama'            => $row['agama'] ?? null,
                'alamat'           => $row['alamat'] ?? null,
                'nama_ayah'        => $row['nama_ayah'] ?? null,
                'pekerjaan_ayah'   => $row['pekerjaan_ayah'] ?? null,
                'nama_ibu'         => $row['nama_ibu'] ?? null,
                'pekerjaan_ibu'    => $row['pekerjaan_ibu'] ?? null,
                'nama_wali'        => $row['nama_wali'] ?? null,
                'pekerjaan_wali'   => $row['pekerjaan_wali'] ?? null,
                'no_hp_ortu'       => $row['no_hp_ortu'] ?? null,
                'asal_sekolah'     => $row['asal_sekolah'] ?? null,
                'peserta_didik_id' => $row['peserta_didik_id'] ?? null,
                'sumber_data'      => 'dapodik',
            ];

            if ($status === 'new') {
                // Set nama_ortu from nama_ayah/nama_ibu
                $saveData['nama_ortu'] = $saveData['nama_ayah'] ?: $saveData['nama_ibu'];
                $saveData['username'] = $saveData['nisn'] ?: 'SISWA' . time() . rand(100,999);
                $saveData['created_at'] = date('Y-m-d H:i:s');

                $siswaModel->insert($saveData);
                $siswaId = $siswaModel->getInsertID();

                // Create user account
                $userModel->insert([
                    'username'       => $saveData['username'],
                    'password'       => password_hash('123123', PASSWORD_DEFAULT),
                    'role'           => 'siswa',
                    'entity_id'      => $siswaId,
                    'is_active'      => 1,
                    'is_first_login' => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
                $inserted++;
            } elseif ($status === 'changed' && !empty($row['local_id'])) {
                $saveData['updated_at'] = date('Y-m-d H:i:s');
                $siswaModel->update($row['local_id'], $saveData);
                $updated++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'inserted' => $inserted,
            'updated' => $updated,
        ]);
    }

    // ==========================================
    // DAPODIK GURU SYNC
    // ==========================================

    public function compareGuruDapodik()
    {
        $input = $this->request->getJSON(true);
        $guruData = $input['guru'] ?? [];
        $guruModel = new GuruModel();

        $results = [];
        foreach ($guruData as $g) {
            $nuptk = $g['nuptk'] ?? ($g['NUPTK'] ?? null);
            $nama = $g['nama'] ?? ($g['nama_ptk'] ?? '');
            $jk = $g['jenis_kelamin'] ?? ($g['jk'] ?? '');
            if ($jk === 'Laki-laki' || $jk === 'L') $jk = 'L';
            elseif ($jk === 'Perempuan' || $jk === 'P') $jk = 'P';

            $dapodikRow = [
                'nuptk'              => $nuptk,
                'nik'                => $g['nik'] ?? null,
                'nama'               => $nama,
                'jenis_kelamin'      => $jk,
                'tempat_lahir'       => $g['tempat_lahir'] ?? null,
                'tanggal_lahir'      => $g['tanggal_lahir'] ?? null,
                'agama'              => $g['agama'] ?? ($g['agama_id_str'] ?? null),
                'alamat'             => $g['alamat_jalan'] ?? ($g['alamat'] ?? null),
                'no_hp'              => $g['no_hp'] ?? ($g['nomor_telepon_seluler'] ?? null),
                'email'              => $g['email'] ?? null,
                'status_kepegawaian' => $g['status_kepegawaian'] ?? ($g['status_kepegawaian_id_str'] ?? null),
                'jabatan'            => $g['jenis_ptk'] ?? ($g['jabatan'] ?? null),
                'gelar_depan'        => $g['gelar_depan'] ?? null,
                'gelar_belakang'     => $g['gelar_belakang'] ?? null,
                'ptk_id'             => $g['ptk_id'] ?? ($g['id_ptk'] ?? null),
            ];

            $existing = $nuptk ? $guruModel->where('nuptk', $nuptk)->first() : null;

            if (!$existing) {
                $results[] = array_merge($dapodikRow, [
                    'status' => 'new', 'changes' => null, 'local_id' => null,
                ]);
            } else {
                $changes = [];
                $fieldsToCompare = ['nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'nik',
                    'no_hp', 'email', 'status_kepegawaian', 'jabatan', 'gelar_depan', 'gelar_belakang'];
                
                foreach ($fieldsToCompare as $field) {
                    $newVal = $dapodikRow[$field] ?? '';
                    $oldVal = $existing[$field] ?? '';
                    if ($newVal && $newVal !== $oldVal) {
                        $changes[] = ['field' => $field, 'old' => $oldVal, 'new' => $newVal];
                    }
                }

                $results[] = array_merge($dapodikRow, [
                    'status' => count($changes) > 0 ? 'changed' : 'same',
                    'changes' => $changes,
                    'local_id' => $existing['id'],
                ]);
            }
        }

        return $this->response->setJSON(['results' => $results]);
    }

    public function applyGuruDapodik()
    {
        $input = $this->request->getJSON(true);
        $data = $input['data'] ?? [];
        $guruModel = new GuruModel();
        $userModel = new UserModel();

        $inserted = 0;
        $updated = 0;

        foreach ($data as $row) {
            $status = $row['status'] ?? 'same';
            if ($status === 'same') continue;

            $saveData = [
                'nuptk'              => $row['nuptk'] ?? null,
                'nik'                => $row['nik'] ?? null,
                'nama'               => $row['nama'] ?? '',
                'jenis_kelamin'      => $row['jenis_kelamin'] ?? 'L',
                'tempat_lahir'       => $row['tempat_lahir'] ?? null,
                'tanggal_lahir'      => $row['tanggal_lahir'] ?? null,
                'agama'              => $row['agama'] ?? null,
                'alamat'             => $row['alamat'] ?? null,
                'no_hp'              => $row['no_hp'] ?? null,
                'email'              => $row['email'] ?? null,
                'status_kepegawaian' => $row['status_kepegawaian'] ?? null,
                'jabatan'            => $row['jabatan'] ?? null,
                'gelar_depan'        => $row['gelar_depan'] ?? null,
                'gelar_belakang'     => $row['gelar_belakang'] ?? null,
                'ptk_id'             => $row['ptk_id'] ?? null,
            ];

            if ($status === 'new') {
                $saveData['created_at'] = date('Y-m-d H:i:s');
                $guruModel->insert($saveData);
                $guruId = $guruModel->getInsertID();

                // Create user account
                $username = $saveData['nuptk'] ?: 'GURU' . time() . rand(100,999);
                $userModel->insert([
                    'username'       => $username,
                    'password'       => password_hash('123123', PASSWORD_DEFAULT),
                    'role'           => 'guru',
                    'entity_id'      => $guruId,
                    'is_active'      => 1,
                    'is_first_login' => 1,
                    'created_at'     => date('Y-m-d H:i:s'),
                ]);
                $inserted++;
            } elseif ($status === 'changed' && !empty($row['local_id'])) {
                $saveData['updated_at'] = date('Y-m-d H:i:s');
                $guruModel->update($row['local_id'], $saveData);
                $updated++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'inserted' => $inserted,
            'updated' => $updated,
        ]);
    }
}
