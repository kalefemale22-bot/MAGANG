2<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiSessionModel extends Model
{
    protected $table = 'absensi_sessions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['jadwal_id', 'guru_id', 'kelas_id', 'rombel_id', 'tanggal', 'opened_at', 'expires_at', 'created_at'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Cek apakah ada session aktif untuk kelas tertentu hari ini.
     */
    public function isSessionActiveForKelas(int $kelasId, string $tanggal): bool
    {
        $session = $this->where('kelas_id', $kelasId)
            ->where('tanggal', $tanggal)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
        return $session !== null;
    }

    /**
     * Ambil session aktif untuk kelas tertentu hari ini.
     */
    public function getActiveSessionForKelas(int $kelasId, string $tanggal): ?array
    {
        return $this->where('kelas_id', $kelasId)
            ->where('tanggal', $tanggal)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
    }

    /**
     * Ambil SEMUA session aktif hari ini (untuk auto-close).
     */
    public function getAllActiveSessionsToday(): array
    {
        $tanggal = date('Y-m-d');
        return $this->where('tanggal', $tanggal)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->findAll();
    }

    /**
     * Cek apakah session sudah ada untuk kelas ini hari ini (apakah sudah di-open).
     */
    public function sessionExistsForKelas(int $kelasId, string $tanggal): bool
    {
        return $this->where('kelas_id', $kelasId)
            ->where('tanggal', $tanggal)
            ->first() !== null;
    }

    /**
     * Hapus session untuk kelas tertentu hari ini.
     */
    public function deleteSessionByKelas(int $kelasId, string $tanggal): void
    {
        $this->where('kelas_id', $kelasId)
            ->where('tanggal', $tanggal)
            ->delete();
    }

    /**
     * Hapus SEMUA session hari ini (setelah auto-close).
     */
    public function deleteAllSessionsToday(): void
    {
        $this->where('tanggal', date('Y-m-d'))->delete();
    }

    /**
     * Get distinct kelas IDs yang punya session aktif hari ini.
     */
    public function getDistinctKelasWithActiveSessionToday(): array
    {
        $db = \Config\Database::connect();
        return $db->table($this->table)
            ->select('DISTINCT kelas_id, tanggal')
            ->where('tanggal', date('Y-m-d'))
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->get()->getResultArray();
    }
}
