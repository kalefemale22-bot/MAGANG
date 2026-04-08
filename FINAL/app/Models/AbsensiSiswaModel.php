<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiSiswaModel extends Model
{
    protected $table = 'absensi_siswa';
    protected $primaryKey = 'id';
    protected $allowedFields = ['siswa_id', 'kelas_id', 'rombel_id', 'jadwal_id', 'tanggal', 'status', 'keterangan', 'foto_surat', 'guru_id', 'created_at'];
    protected $useTimestamps = false;
}
