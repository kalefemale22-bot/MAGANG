<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiSiswaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'rombel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'jadwal_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tanggal' => ['type' => 'DATE'],
            'status' => ['type' => 'ENUM', 'constraint' => ['Hadir', 'Sakit', 'Izin', 'Alpha']],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['siswa_id', 'jadwal_id', 'tanggal']);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('jadwal_id', 'jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('absensi_siswa');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_siswa');
    }
}
