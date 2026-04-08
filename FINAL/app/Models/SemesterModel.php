<?php

namespace App\Models;

use CodeIgniter\Model;

class SemesterModel extends Model
{
    protected $table = 'semester';
    protected $primaryKey = 'id';
    protected $allowedFields = ['tahun_ajaran_id', 'nama_semester', 'is_aktif'];
    protected $useTimestamps = false;

    public function getAktif()
    {
        return $this->where('is_aktif', 1)->first();
    }
}
