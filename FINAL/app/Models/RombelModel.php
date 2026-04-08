<?php

namespace App\Models;

use CodeIgniter\Model;

class RombelModel extends Model
{
    protected $table = 'rombel';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nama_rombel', 'tingkat', 'mapel_id', 'guru_id', 'semester_id', 'rombel_asal_id', 'created_at'];
    protected $useTimestamps = false;
}
