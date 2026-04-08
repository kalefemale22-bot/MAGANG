<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMonitoringFlagToSiswa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('siswa', [
            'is_monitoring' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'status',
                'comment'    => '1 = ditunjuk admin untuk mengisi monitoring guru',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('siswa', 'is_monitoring');
    }
}
