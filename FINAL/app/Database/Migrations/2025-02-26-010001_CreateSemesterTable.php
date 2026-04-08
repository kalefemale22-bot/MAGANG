<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSemesterTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nama_semester' => ['type' => 'ENUM', 'constraint' => ['Ganjil', 'Genap']],
            'is_aktif' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tahun_ajaran_id', 'tahun_ajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('semester');
    }

    public function down()
    {
        $this->forge->dropTable('semester');
    }
}
