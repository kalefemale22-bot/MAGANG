<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'aksi', 'tabel', 'record_id', 'detail', 'ip_address', 'created_at'];
    protected $useTimestamps = false;

    public function logActivity($userId, $aksi, $tabel = null, $recordId = null, $detail = null)
    {
        return $this->insert([
            'user_id'    => $userId,
            'aksi'       => $aksi,
            'tabel'      => $tabel,
            'record_id'  => $recordId,
            'detail'     => $detail ? json_encode($detail) : null,
            'ip_address' => service('request')->getIPAddress(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
