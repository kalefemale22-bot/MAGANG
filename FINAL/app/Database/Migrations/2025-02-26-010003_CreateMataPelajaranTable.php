<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMataPelajaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kode' => ['type' => 'VARCHAR', 'constraint' => 10],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 50],
            'kelompok' => ['type' => 'VARCHAR', 'constraint' => 30],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('mata_pelajaran');
    }

    public function down()
    {
        $this->forge->dropTable('mata_pelajaran');
    }
}
