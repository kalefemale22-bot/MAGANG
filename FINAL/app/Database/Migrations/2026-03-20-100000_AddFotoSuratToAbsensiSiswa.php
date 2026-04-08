<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoSuratToAbsensiSiswa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('absensi_siswa', [
            'foto_surat' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'default'    => null,
                'after'      => 'keterangan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('absensi_siswa', 'foto_surat');
    }
}
