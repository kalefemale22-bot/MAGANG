<?php

namespace App\Models;

use CodeIgniter\Model;

class EkstrakurikulerModel extends Model
{
    protected $table            = 'ekstrakurikuler';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['nama_ekskul', 'pembina_id'];
}
