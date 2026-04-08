<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiModel extends Model
{
    protected $table = 'nilai';
    protected $primaryKey = 'id';
    protected $allowedFields = ['siswa_id', 'mapel_id', 'guru_id', 'semester_id', 'jenis_nilai', 'nilai', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
