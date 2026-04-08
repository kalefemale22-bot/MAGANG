<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileFieldsToGuru extends Migration
{
    public function up()
    {
        $this->forge->addColumn('guru', [
            'nik'                => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'nuptk'],
            'tempat_lahir'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'jenis_kelamin'],
            'tanggal_lahir'      => ['type' => 'DATE', 'null' => true, 'after' => 'tempat_lahir'],
            'agama'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'tanggal_lahir'],
            'alamat'             => ['type' => 'TEXT', 'null' => true, 'after' => 'agama'],
            'no_hp'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'alamat'],
            'email'              => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'no_hp'],
            'status_kepegawaian' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'after' => 'email'],
            'jabatan'            => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'status_kepegawaian'],
            'gelar_depan'        => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'jabatan'],
            'gelar_belakang'     => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'gelar_depan'],
            'ptk_id'             => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'foto'],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true, 'after' => 'created_at'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('guru', [
            'nik', 'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat',
            'no_hp', 'email', 'status_kepegawaian', 'jabatan',
            'gelar_depan', 'gelar_belakang', 'ptk_id', 'updated_at',
        ]);
    }
}
