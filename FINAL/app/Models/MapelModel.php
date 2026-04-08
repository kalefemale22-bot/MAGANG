<?php

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table = 'mata_pelajaran';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode', 'nama', 'kelompok', 'created_at'];
    protected $useTimestamps = false;
}
