<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLaporanKehadiranGuruTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jadwal_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'mapel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'rombel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tanggal' => ['type' => 'DATE'],
            'status' => ['type' => 'ENUM', 'constraint' => ['hadir', 'tugas', 'tidak_hadir']],
            'keterangan' => ['type' => 'TEXT', 'null' => true],
            'is_verified' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'verified_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['siswa_id', 'jadwal_id', 'tanggal']);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jadwal_id', 'jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('mapel_id', 'mata_pelajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('rombel_id', 'rombel', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('laporan_kehadiran_guru');
    }

    public function down()
    {
        $this->forge->dropTable('laporan_kehadiran_guru');
    }
}
