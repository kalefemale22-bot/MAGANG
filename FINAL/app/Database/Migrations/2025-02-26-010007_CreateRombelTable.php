<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRombelTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_rombel' => ['type' => 'VARCHAR', 'constraint' => 30],
            'tingkat' => ['type' => 'ENUM', 'constraint' => ['XI', 'XII']],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'rombel_asal_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rombel');
    }

    public function down()
    {
        $this->forge->dropTable('rombel');
    }
}
