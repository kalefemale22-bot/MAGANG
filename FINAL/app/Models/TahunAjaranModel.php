<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAjaranModel extends Model
{
    protected $table = 'tahun_ajaran';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama', 'is_aktif'];
    protected $useTimestamps = false;

    public function getAktif()
    {
        return $this->where('is_aktif', 1)->first();
    }
}
