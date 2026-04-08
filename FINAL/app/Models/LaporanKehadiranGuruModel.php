<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanKehadiranGuruModel extends Model
{
    protected $table = 'laporan_kehadiran_guru';
    protected $primaryKey = 'id';
    protected $allowedFields = ['siswa_id', 'jadwal_id', 'guru_id', 'mapel_id', 'kelas_id', 'rombel_id', 'tanggal', 'status', 'keterangan', 'is_verified', 'verified_by', 'created_at'];
    protected $useTimestamps = false;
}
