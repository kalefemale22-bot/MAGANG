<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNilaiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis_nilai' => ['type' => 'ENUM', 'constraint' => ['UH1', 'UH2', 'UH3', 'UTS', 'UAS', 'Tugas', 'Praktik']],
            'nilai' => ['type' => 'DECIMAL', 'constraint' => '5,2', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('nilai');
    }

    public function down()
    {
        $this->forge->dropTable('nilai');
    }
}
