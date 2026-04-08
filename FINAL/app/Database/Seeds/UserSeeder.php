<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $defaultPassword = password_hash('123123', PASSWORD_BCRYPT);

        // Create user accounts for all guru
        $allGuru = $this->db->table('guru')->get()->getResultArray();
        $guruCount = 0;
        foreach ($allGuru as $guru) {
            $this->db->table('users')->insert([
                'username'       => $guru['nuptk'],
                'password'       => $defaultPassword,
                'role'           => 'guru',
                'entity_id'      => $guru['id'],
                'is_active'      => 1,
                'is_first_login' => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            $guruCount++;
        }

        // Create user accounts for all siswa
        $allSiswa = $this->db->table('siswa')->where('status', 'aktif')->get()->getResultArray();
        $siswaCount = 0;
        $batchData = [];
        foreach ($allSiswa as $siswa) {
            $batchData[] = [
                'username'       => $siswa['username'],
                'password'       => $defaultPassword,
                'role'           => 'siswa',
                'entity_id'      => $siswa['id'],
                'is_active'      => 1,
                'is_first_login' => 1,
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            $siswaCount++;

            if (count($batchData) >= 100) {
                $this->db->table('users')->insertBatch($batchData);
                $batchData = [];
            }
        }
        if (!empty($batchData)) {
            $this->db->table('users')->insertBatch($batchData);
        }

        // Create 1 admin account (first guru as admin too, or dedicated)
        $this->db->table('users')->insert([
            'username'       => 'admin',
            'password'       => $defaultPassword,
            'role'           => 'admin',
            'entity_id'      => 1,
            'is_active'      => 1,
            'is_first_login' => 0,
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        echo "UserSeeder: $guruCount guru users, $siswaCount siswa users, 1 admin created.\n";
        echo "Admin login: admin / 123123\n";
        echo "Guru login: NUPTK / 123123 (e.g. 5550768669130060)\n";
        echo "Siswa login: nomor_siswa / 123123 (e.g. 64509133)\n";
    }
}
