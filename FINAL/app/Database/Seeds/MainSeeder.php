<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        $this->call('TahunAjaranSeeder');
        $this->call('SemesterSeeder');
        $this->call('MapelSeeder');
        $this->call('GuruSeeder');
        $this->call('KelasSeeder');
        $this->call('SiswaSeeder');
        $this->call('UserSeeder');
        $this->call('JadwalSeeder');

        echo "All seeders completed!\n";
    }
}
