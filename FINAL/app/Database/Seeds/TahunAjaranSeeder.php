<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class TahunAjaranSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('tahun_ajaran')->insert([
            'nama' => '2025/2026',
            'is_aktif' => 1,
        ]);
        echo "TahunAjaran seeded.\n";
    }
}
