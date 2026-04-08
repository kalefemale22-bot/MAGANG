<?php

namespace App\Models;

use CodeIgniter\Model;

class CatatanRaporModel extends Model
{
    protected $table            = 'catatan_rapor';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'siswa_id', 'semester_id', 'sakit', 'izin', 'alpa', 'catatan_wali', 'is_locked'
    ];
}
