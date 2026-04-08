<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'rombel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'hari' => ['type' => 'ENUM', 'constraint' => ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat']],
            'jam_ke' => ['type' => 'TINYINT', 'constraint' => 2],
            'jam_mulai' => ['type' => 'TIME'],
            'jam_selesai' => ['type' => 'TIME'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('jadwal');
    }

    public function down()
    {
        $this->forge->dropTable('jadwal');
    }
}
