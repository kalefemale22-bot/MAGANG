<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGuruMapelKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'rombel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('guru_mapel_kelas');
    }

    public function down()
    {
        $this->forge->dropTable('guru_mapel_kelas');
    }
}
