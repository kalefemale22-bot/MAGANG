<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class GuruSeeder extends Seeder
{
    public function run()
    {
        $spreadsheet = IOFactory::load(ROOTPATH . 'tes21.xlsx');
        $sheet = $spreadsheet->getActiveSheet();
        $highestRow = $sheet->getHighestRow();

        $count = 0;
        for ($row = 2; $row <= $highestRow; $row++) {
            $nuptk = trim($sheet->getCell('A' . $row)->getValue());
            $nama  = trim($sheet->getCell('D' . $row)->getValue());
            $jk    = trim($sheet->getCell('E' . $row)->getValue());

            if (empty($nama)) continue;

            $this->db->table('guru')->insert([
                'nuptk'         => $nuptk,
                'nama'          => $nama,
                'jenis_kelamin' => ($jk === 'L') ? 'L' : 'P',
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
            $count++;
        }

        echo "GuruSeeder: $count guru imported from tes21.xlsx\n";
    }
}
