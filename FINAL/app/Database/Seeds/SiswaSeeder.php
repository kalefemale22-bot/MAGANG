<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class SiswaSeeder extends Seeder
{
    public function run()
    {
        $spreadsheet = IOFactory::load(ROOTPATH . 'tes22.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        // Get all kelas for distribution
        $allKelas = $this->db->table('kelas')->orderBy('id')->get()->getResultArray();
        $kelasMap = [];
        foreach ($allKelas as $k) {
            $kelasMap[$k['nama_kelas']] = $k['id'];
        }

        // Distribute students across kelas
        // X: 7 kelas, XI: 8 kelas, XII: 6 kelas = 21 kelas
        // 712 students / 21 ≈ 34 per kelas
        $kelasIds = array_values($kelasMap);
        $kelasCount = count($kelasIds);

        $count = 0;
        $batchData = [];

        for ($row = 2; $row <= $highestRow; $row++) {
            $username = trim($sheet->getCell('A' . $row)->getValue());
            $nama     = trim($sheet->getCell('D' . $row)->getValue());
            $jk       = trim($sheet->getCell('E' . $row)->getValue());
            $nisn     = trim($sheet->getCell('F' . $row)->getValue());
            $kelas    = trim($sheet->getCell('G' . $row)->getValue());

            if (empty($nama)) continue;

            // Assign kelas: use Excel data if available, otherwise distribute evenly
            $kelasId = null;
            if (!empty($kelas) && isset($kelasMap[$kelas])) {
                $kelasId = $kelasMap[$kelas];
            } else {
                // Round-robin distribution
                $kelasId = $kelasIds[$count % $kelasCount];
            }

            $batchData[] = [
                'username'       => $username,
                'nisn'           => !empty($nisn) ? $nisn : null,
                'nama'           => $nama,
                'jenis_kelamin'  => ($jk === 'L') ? 'L' : 'P',
                'kelas_id'       => $kelasId,
                'tahun_masuk'    => 2024,
                'status'         => 'aktif',
                'sumber_data'    => 'excel',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ];
            $count++;

            // Insert in batches of 100
            if (count($batchData) >= 100) {
                $this->db->table('siswa')->insertBatch($batchData);
                $batchData = [];
            }
        }

        // Insert remaining
        if (!empty($batchData)) {
            $this->db->table('siswa')->insertBatch($batchData);
        }

        echo "SiswaSeeder: $count siswa imported from tes22.xlsx\n";

        // Show distribution
        $dist = $this->db->query("SELECT k.nama_kelas, COUNT(s.id) as jumlah FROM kelas k LEFT JOIN siswa s ON s.kelas_id = k.id GROUP BY k.id, k.nama_kelas ORDER BY k.id")->getResultArray();
        foreach ($dist as $d) {
            echo "  {$d['nama_kelas']}: {$d['jumlah']} siswa\n";
        }
    }
}
