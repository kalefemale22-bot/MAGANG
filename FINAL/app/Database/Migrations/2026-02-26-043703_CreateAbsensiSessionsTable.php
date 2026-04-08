<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiSessionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'jadwal_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'guru_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'kelas_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'rombel_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'tanggal'     => ['type' => 'DATE'],
            'opened_at'   => ['type' => 'DATETIME'],
            'expires_at'  => ['type' => 'DATETIME'],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('jadwal_id', 'jadwal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('guru_id', 'guru', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kelas_id', 'kelas', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('absensi_sessions');
    }

    public function down()
    {
        $this->forge->dropTable('absensi_sessions');
    }
}
