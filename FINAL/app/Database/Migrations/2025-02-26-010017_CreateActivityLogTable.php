<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'aksi' => ['type' => 'VARCHAR', 'constraint' => 100],
            'tabel' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'record_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'detail' => ['type' => 'TEXT', 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('activity_log');
    }

    public function down()
    {
        $this->forge->dropTable('activity_log');
    }
}
