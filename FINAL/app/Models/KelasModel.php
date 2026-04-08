<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table = 'kelas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_kelas', 'tingkat', 'jurusan', 'wali_kelas_id', 'tahun_ajaran_id', 'created_at'];
    protected $useTimestamps = false;

    public function getWithWali($id = null)
    {
        $builder = $this->select('kelas.*, guru.nama as wali_nama')
            ->join('guru', 'guru.id = kelas.wali_kelas_id', 'left');
        if ($id) return $builder->where('kelas.id', $id)->first();
        return $builder->orderBy('kelas.tingkat')->orderBy('kelas.nama_kelas')->findAll();
    }

    public function getJumlahSiswa($kelasId)
    {
        return (new SiswaModel())->where('kelas_id', $kelasId)->where('status', 'aktif')->countAllResults();
    }
}
