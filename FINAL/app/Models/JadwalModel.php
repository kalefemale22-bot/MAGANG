<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalModel extends Model
{
    protected $table = 'jadwal';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kelas_id', 'rombel_id', 'mapel_id', 'guru_id', 'semester_id', 'hari', 'jam_ke', 'jam_mulai', 'jam_selesai', 'created_at'];
    protected $useTimestamps = false;

    public function getJadwalKelas($kelasId, $semesterId)
    {
        return $this->select('jadwal.*, mata_pelajaran.nama as mapel_nama, guru.nama as guru_nama')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('guru', 'guru.id = jadwal.guru_id')
            ->where('jadwal.kelas_id', $kelasId)
            ->where('jadwal.semester_id', $semesterId)
            ->orderBy('FIELD(jadwal.hari, "Senin","Selasa","Rabu","Kamis","Jumat")')
            ->orderBy('jadwal.jam_ke')
            ->findAll();
    }

    public function getJadwalGuru($guruId, $semesterId)
    {
        return $this->select('jadwal.*, mata_pelajaran.nama as mapel_nama, kelas.nama_kelas, rombel.nama_rombel')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id', 'left')
            ->join('rombel', 'rombel.id = jadwal.rombel_id', 'left')
            ->where('jadwal.guru_id', $guruId)
            ->where('jadwal.semester_id', $semesterId)
            ->orderBy('FIELD(jadwal.hari, "Senin","Selasa","Rabu","Kamis","Jumat")')
            ->orderBy('jadwal.jam_ke')
            ->findAll();
    }

    public function getJadwalHariIni($kelasId = null, $rombelIds = [], $semesterId = null)
    {
        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat'];
        $hariIni = $hariMap[date('l')] ?? null;

        if (!$hariIni) return [];

        $builder = $this->select('jadwal.*, mata_pelajaran.nama as mapel_nama, guru.nama as guru_nama, kelas.nama_kelas, rombel.nama_rombel')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('guru', 'guru.id = jadwal.guru_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id', 'left')
            ->join('rombel', 'rombel.id = jadwal.rombel_id', 'left')
            ->where('jadwal.hari', $hariIni)
            ->where('jadwal.semester_id', $semesterId);

        if ($kelasId && !empty($rombelIds)) {
            $builder->groupStart()
                ->where('jadwal.kelas_id', $kelasId)
                ->orWhereIn('jadwal.rombel_id', $rombelIds)
                ->groupEnd();
        } elseif ($kelasId) {
            $builder->where('jadwal.kelas_id', $kelasId);
        }

        return $builder->orderBy('jadwal.jam_ke')->findAll();
    }

    /**
     * Ambil semua jadwal untuk siswa tertentu pada hari tertentu.
     */
    public function getJadwalSiswaHariIni(int $siswaId, string $tanggal, int $semesterId): array
    {
        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
        $dayOfWeek = date('l', strtotime($tanggal));
        $hari = $hariMap[$dayOfWeek] ?? null;

        if (!$hari) return [];

        // Ambil kelas_id siswa dari tabel siswa
        $db = \Config\Database::connect();
        $siswa = $db->table('siswa')->select('kelas_id')->where('id', $siswaId)->get()->getRowArray();

        return $this->select('jadwal.*, mata_pelajaran.nama as mapel_nama, guru.nama as guru_nama, kelas.nama_kelas')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('guru', 'guru.id = jadwal.guru_id')
            ->join('kelas', 'kelas.id = jadwal.kelas_id', 'left')
            ->where('jadwal.hari', $hari)
            ->where('jadwal.semester_id', $semesterId)
            ->where('jadwal.kelas_id', $siswa['kelas_id'] ?? null)
            ->orderBy('jadwal.jam_ke')
            ->findAll();
    }

    /**
     * Ambil semua jadwal untuk kelas tertentu pada hari tertentu.
     */
    public function getJadwalKelasTanggal(int $kelasId, string $tanggal, int $semesterId): array
    {
        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu'];
        $dayOfWeek = date('l', strtotime($tanggal));
        $hari = $hariMap[$dayOfWeek] ?? null;

        if (!$hari) return [];

        return $this->select('jadwal.*, mata_pelajaran.nama as mapel_nama, guru.nama as guru_nama')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal.mapel_id')
            ->join('guru', 'guru.id = jadwal.guru_id')
            ->where('jadwal.hari', $hari)
            ->where('jadwal.semester_id', $semesterId)
            ->where('jadwal.kelas_id', $kelasId)
            ->orderBy('jadwal.jam_ke')
            ->findAll();
    }

    /**
     * Ambil semua distinct kelas_id yang punya jadwal pada hari tertentu di semester aktif.
     */
    public function getDistinctKelasHariIni(int $semesterId): array
    {
        $hariMap = ['Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat'];
        $hari = $hariMap[date('l')] ?? null;

        if (!$hari) return [];

        $db = \Config\Database::connect();
        return $db->table('jadwal')
            ->select('DISTINCT kelas_id')
            ->where('hari', $hari)
            ->where('semester_id', $semesterId)
            ->where('kelas_id IS NOT NULL')
            ->get()->getResultArray();
    }

    /**
     * Jam selesai terakhir jadwal untuk kelas tertentu hari ini.
     */
    public function getJamSelesaiTerakhir(int $kelasId, string $tanggal, int $semesterId): string
    {
        $jadwal = $this->getJadwalKelasTanggal($kelasId, $tanggal, $semesterId);
        if (empty($jadwal)) return '15:30';

        $last = end($jadwal);
        return $last['jam_selesai'] ?? '15:30';
    }
}
