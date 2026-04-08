<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['username', 'password', 'role', 'entity_id', 'is_active', 'is_first_login', 'last_login', 'created_at', 'updated_at'];
    protected $useTimestamps = true;

    public function getWithEntity($id)
    {
        $user = $this->find($id);
        if (!$user) return null;

        if ($user['role'] === 'guru') {
            $user['entity'] = (new GuruModel())->find($user['entity_id']);
        } elseif ($user['role'] === 'siswa') {
            $user['entity'] = (new SiswaModel())->find($user['entity_id']);
        }

        return $user;
    }
}
