<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 15],
            'nisn' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'nis' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'nama' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P']],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true],
            'nama_ortu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_hp_ortu' => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tahun_masuk' => ['type' => 'YEAR', 'null' => true],
            'tahun_lulus' => ['type' => 'YEAR', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['aktif', 'lulus', 'pindah', 'keluar'], 'default' => 'aktif'],
            'foto' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sumber_data' => ['type' => 'ENUM', 'constraint' => ['dapodik', 'manual', 'excel'], 'default' => 'excel'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('username');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('siswa');
    }

    public function down()
    {
        $this->forge->dropTable('siswa');
    }
}
