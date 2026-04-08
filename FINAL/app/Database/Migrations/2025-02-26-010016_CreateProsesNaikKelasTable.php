<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProsesNaikKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tahun_ajaran_lama' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'tahun_ajaran_baru' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'total_naik' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_lulus' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_tinggal' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_pindah' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'dieksekusi_oleh' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'dieksekusi_at' => ['type' => 'DATETIME', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['draft', 'confirmed'], 'default' => 'draft'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('tahun_ajaran_lama', 'tahun_ajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tahun_ajaran_baru', 'tahun_ajaran', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('dieksekusi_oleh', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('proses_naik_kelas');
    }

    public function down()
    {
        $this->forge->dropTable('proses_naik_kelas');
    }
}
