<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaRombelTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'rombel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['siswa_id', 'rombel_id', 'semester_id']);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('siswa_rombel');
    }

    public function down()
    {
        $this->forge->dropTable('siswa_rombel');
    }
}
