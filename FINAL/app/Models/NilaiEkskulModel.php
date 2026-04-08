<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiEkskulModel extends Model
{
    protected $table            = 'nilai_ekskul';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'siswa_id', 'ekskul_id', 'semester_id', 'nilai', 'deskripsi'
    ];
}
