<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNilaiEkskulTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'ekskul_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai' => ['type' => 'ENUM', 'constraint' => ['A', 'B', 'C', 'D']],
            'deskripsi' => ['type' => 'TEXT', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('ekskul_id', 'ekstrakurikuler', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nilai_ekskul');
    }

    public function down()
    {
        $this->forge->dropTable('nilai_ekskul');
    }
}
