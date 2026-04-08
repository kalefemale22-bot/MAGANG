<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class KelasSeeder extends Seeder
{
    public function run()
    {
        $tahunAjaran = $this->db->table('tahun_ajaran')->where('is_aktif', 1)->get()->getRow();

        // Parse wali kelas from tes21.xlsx
        $spreadsheet = IOFactory::load(ROOTPATH . 'tes21.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // Build wali kelas map: kelas_name => guru NUPTK
        $waliKelasMap = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            $nuptk = trim($sheet->getCell('A' . $row)->getValue());
            $waliKelas = trim($sheet->getCell('G' . $row)->getValue());

            if (!empty($waliKelas) && $waliKelas !== '-') {
                $waliKelasMap[$waliKelas] = $nuptk;
            }
        }

        // Define all 21 kelas
        $kelasList = [
            // Kelas X (7)
            ['nama_kelas' => 'X-1',  'tingkat' => 'X',   'jurusan' => null],
            ['nama_kelas' => 'X-2',  'tingkat' => 'X',   'jurusan' => null],
            ['nama_kelas' => 'X-3',  'tingkat' => 'X',   'jurusan' => null],
            ['nama_kelas' => 'X-4',  'tingkat' => 'X',   'jurusan' => null],
            ['nama_kelas' => 'X-5',  'tingkat' => 'X',   'jurusan' => null],
            ['nama_kelas' => 'X-6',  'tingkat' => 'X',   'jurusan' => null],
            ['nama_kelas' => 'X-7',  'tingkat' => 'X',   'jurusan' => null],
            // Kelas XI (8)
            ['nama_kelas' => 'XI-1', 'tingkat' => 'XI',  'jurusan' => null],
            ['nama_kelas' => 'XI-2', 'tingkat' => 'XI',  'jurusan' => null],
            ['nama_kelas' => 'XI-3', 'tingkat' => 'XI',  'jurusan' => null],
            ['nama_kelas' => 'XI-4', 'tingkat' => 'XI',  'jurusan' => null],
            ['nama_kelas' => 'XI-5', 'tingkat' => 'XI',  'jurusan' => null],
            ['nama_kelas' => 'XI-6', 'tingkat' => 'XI',  'jurusan' => null],
            ['nama_kelas' => 'XI-7', 'tingkat' => 'XI',  'jurusan' => null],
            ['nama_kelas' => 'XI-8', 'tingkat' => 'XI',  'jurusan' => null],
            // Kelas XII (6) - with jurusan
            ['nama_kelas' => 'XII MIPA-1', 'tingkat' => 'XII', 'jurusan' => 'MIPA'],
            ['nama_kelas' => 'XII MIPA-2', 'tingkat' => 'XII', 'jurusan' => 'MIPA'],
            ['nama_kelas' => 'XII MIPA-3', 'tingkat' => 'XII', 'jurusan' => 'MIPA'],
            ['nama_kelas' => 'XII IPS-1',  'tingkat' => 'XII', 'jurusan' => 'IPS'],
            ['nama_kelas' => 'XII IPS-2',  'tingkat' => 'XII', 'jurusan' => 'IPS'],
            ['nama_kelas' => 'XII IPS-3',  'tingkat' => 'XII', 'jurusan' => 'IPS'],
        ];

        foreach ($kelasList as $kelas) {
            // Map wali kelas short name to database kelas name
            $waliId = null;
            // Check direct match
            if (isset($waliKelasMap[$kelas['nama_kelas']])) {
                $guru = $this->db->table('guru')->where('nuptk', $waliKelasMap[$kelas['nama_kelas']])->get()->getRow();
                if ($guru) $waliId = $guru->id;
            }
            // Check XII mapping (XII-1 -> XII MIPA-1, etc.)
            if ($waliId === null && $kelas['tingkat'] === 'XII') {
                $shortNames = [
                    'XII MIPA-1' => 'XII-1',
                    'XII MIPA-2' => 'XII-2',
                    'XII MIPA-3' => 'XII-3',
                    'XII IPS-1'  => 'XII-4',
                    'XII IPS-2'  => 'XII-5',
                    'XII IPS-3'  => 'XII-6',
                ];
                $shortName = $shortNames[$kelas['nama_kelas']] ?? null;
                if ($shortName && isset($waliKelasMap[$shortName])) {
                    $guru = $this->db->table('guru')->where('nuptk', $waliKelasMap[$shortName])->get()->getRow();
                    if ($guru) $waliId = $guru->id;
                }
            }

            $this->db->table('kelas')->insert([
                'nama_kelas'      => $kelas['nama_kelas'],
                'tingkat'         => $kelas['tingkat'],
                'jurusan'         => $kelas['jurusan'],
                'wali_kelas_id'   => $waliId,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'created_at'      => date('Y-m-d H:i:s'),
            ]);
        }

        echo "KelasSeeder: " . count($kelasList) . " kelas created.\n";
        echo "Wali kelas mapped: " . implode(', ', array_keys($waliKelasMap)) . "\n";
    }
}
