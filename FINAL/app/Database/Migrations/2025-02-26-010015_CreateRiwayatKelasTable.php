<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRiwayatKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status_akhir' => ['type' => 'ENUM', 'constraint' => ['naik_kelas', 'lulus', 'pindah', 'keluar', 'tinggal_kelas'], 'null' => true],
            'catatan' => ['type' => 'TEXT', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tahun_ajaran_id', 'tahun_ajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('riwayat_kelas');
    }

    public function down()
    {
        $this->forge->dropTable('riwayat_kelas');
    }
}
