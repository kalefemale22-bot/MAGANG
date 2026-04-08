<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MapelSeeder extends Seeder
{
    public function run()
    {
        $mapel = [
            ['kode' => 'PAI',   'nama' => 'PAI/MULOK (BTA)',    'kelompok' => 'Wajib A'],
            ['kode' => 'PPKN',  'nama' => 'PPKn',               'kelompok' => 'Wajib A'],
            ['kode' => 'BIND',  'nama' => 'B. Indonesia',        'kelompok' => 'Wajib A'],
            ['kode' => 'BING',  'nama' => 'B. Inggris',         'kelompok' => 'Wajib A'],
            ['kode' => 'MTK',   'nama' => 'Matematika',         'kelompok' => 'Wajib A'],
            ['kode' => 'SJR',   'nama' => 'Sejarah',            'kelompok' => 'Wajib A'],
            ['kode' => 'SBD',   'nama' => 'Seni Budaya',        'kelompok' => 'Wajib B'],
            ['kode' => 'PJK',   'nama' => 'Penjasorkes',        'kelompok' => 'Wajib B'],
            ['kode' => 'PKWU',  'nama' => 'PKWU',               'kelompok' => 'Wajib B'],
            ['kode' => 'INF',   'nama' => 'Informatika',        'kelompok' => 'Wajib B'],
            ['kode' => 'FIS',   'nama' => 'Fisika',             'kelompok' => 'Peminatan'],
            ['kode' => 'KIM',   'nama' => 'Kimia',              'kelompok' => 'Peminatan'],
            ['kode' => 'BIO',   'nama' => 'Biologi',            'kelompok' => 'Peminatan'],
            ['kode' => 'EKO',   'nama' => 'Ekonomi',            'kelompok' => 'Peminatan'],
            ['kode' => 'SOS',   'nama' => 'Sosiologi',          'kelompok' => 'Peminatan'],
            ['kode' => 'GEO',   'nama' => 'Geografi',           'kelompok' => 'Peminatan'],
            ['kode' => 'BK',    'nama' => 'BK',                 'kelompok' => 'Layanan'],
        ];

        foreach ($mapel as &$m) {
            $m['created_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('mata_pelajaran')->insertBatch($mapel);
        echo "MapelSeeder: " . count($mapel) . " mata pelajaran seeded.\n";
    }
}
