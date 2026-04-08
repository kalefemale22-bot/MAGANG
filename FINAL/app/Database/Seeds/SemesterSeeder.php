<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SemesterSeeder extends Seeder
{
    public function run()
    {
        $tahunAjaran = $this->db->table('tahun_ajaran')->where('is_aktif', 1)->get()->getRow();

        $this->db->table('semester')->insertBatch([
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'nama_semester' => 'Ganjil',
                'is_aktif' => 0,
            ],
            [
                'tahun_ajaran_id' => $tahunAjaran->id,
                'nama_semester' => 'Genap',
                'is_aktif' => 1,
            ],
        ]);
        echo "Semester seeded.\n";
    }
}
