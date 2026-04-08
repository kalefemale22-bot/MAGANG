<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCatatanRaporTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'siswa_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'semester_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'sakit' => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'izin' => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'alpa' => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'catatan_wali' => ['type' => 'TEXT', 'null' => true],
            'is_locked' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('siswa_id', 'siswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('semester_id', 'semester', 'id', 'CASCADE', 'CASCADE');
        // uniqueness per siswa per semester
        $this->forge->addUniqueKey(['siswa_id', 'semester_id']);
        $this->forge->createTable('catatan_rapor');
    }

    public function down()
    {
        $this->forge->dropTable('catatan_rapor');
    }
}
