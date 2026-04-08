<?php

namespace App\Models;

use CodeIgniter\Model;

class GuruModel extends Model
{
    protected $table = 'guru';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nuptk', 'nik', 'nama', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'no_hp', 'email', 'status_kepegawaian', 'jabatan', 'gelar_depan', 'gelar_belakang', 'foto', 'ptk_id', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    public function getWaliKelas($guruId)
    {
        return (new KelasModel())->where('wali_kelas_id', $guruId)->first();
    }

    public function getMapelDiajarkan($guruId, $semesterId)
    {
        return $this->db->table('guru_mapel_kelas gmk')
            ->join('mata_pelajaran mp', 'mp.id = gmk.mapel_id')
            ->where('gmk.guru_id', $guruId)
            ->where('gmk.semester_id', $semesterId)
            ->groupBy('mp.id')
            ->get()->getResultArray();
    }
}
