<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBobotToMataPelajaran extends Migration
{
    public function up()
    {
        $fields = [
            'bobot_uh' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 50,
            ],
            'bobot_tugas' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'bobot_praktik' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'bobot_uts' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 25,
            ],
            'bobot_uas' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 25,
            ],
        ];
        $this->forge->addColumn('mata_pelajaran', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('mata_pelajaran', ['bobot_uh', 'bobot_tugas', 'bobot_praktik', 'bobot_uts', 'bobot_uas']);
    }
}
