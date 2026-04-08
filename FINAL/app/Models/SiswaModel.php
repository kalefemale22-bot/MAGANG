<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table = 'siswa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'nisn', 'nis', 'nik', 'nama', 'jenis_kelamin', 'agama', 'alamat', 'tempat_lahir', 'tanggal_lahir', 'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu', 'nama_wali', 'pekerjaan_wali', 'nama_ortu', 'no_hp_ortu', 'asal_sekolah', 'kelas_id', 'tahun_masuk', 'tahun_lulus', 'status', 'is_monitoring', 'foto', 'sumber_data', 'peserta_didik_id', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    public function getWithKelas($id)
    {
        return $this->select('siswa.*, kelas.nama_kelas, kelas.tingkat')
            ->join('kelas', 'kelas.id = siswa.kelas_id', 'left')
            ->where('siswa.id', $id)
            ->first();
    }

    public function getByKelas($kelasId)
    {
        return $this->where('kelas_id', $kelasId)
            ->where('status', 'aktif')
            ->orderBy('nama', 'ASC')
            ->findAll();
    }
}
