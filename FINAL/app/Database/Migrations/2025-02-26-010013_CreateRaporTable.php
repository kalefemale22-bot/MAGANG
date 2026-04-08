<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRaporTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'nilai_uh' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_uts' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_uas' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'nilai_akhir' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'predikat' => ['type' => 'VARCHAR', 'constraint' => 2, 'null' => true],
            'catatan_guru' => ['type' => 'TEXT', 'null' => true],
            'generated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('rapor');
    }

    public function down()
    {
        $this->forge->dropTable('rapor');
    }
}
