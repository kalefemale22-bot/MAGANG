<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nama_kelas' => ['type' => 'VARCHAR', 'constraint' => 15],
            'tingkat' => ['type' => 'ENUM', 'constraint' => ['X', 'XI', 'XII']],
            'jurusan' => ['type' => 'ENUM', 'constraint' => ['MIPA', 'IPS'], 'null' => true],
            'wali_kelas_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tahun_ajaran_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('wali_kelas_id', 'guru', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('tahun_ajaran_id', 'tahun_ajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('kelas');
    }

    public function down()
    {
        $this->forge->dropTable('kelas');
    }
}
