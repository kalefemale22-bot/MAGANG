<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBukuIndukFieldsToSiswa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('siswa', [
            'nik'              => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'nis'],
            'agama'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true, 'after' => 'jenis_kelamin'],
            'alamat'           => ['type' => 'TEXT', 'null' => true, 'after' => 'agama'],
            'nama_ayah'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'alamat'],
            'pekerjaan_ayah'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'nama_ayah'],
            'nama_ibu'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'pekerjaan_ayah'],
            'pekerjaan_ibu'    => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'nama_ibu'],
            'nama_wali'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'pekerjaan_ibu'],
            'pekerjaan_wali'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'nama_wali'],
            'asal_sekolah'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'pekerjaan_wali'],
            'peserta_didik_id' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true, 'after' => 'sumber_data'],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('siswa', [
            'nik', 'agama', 'alamat', 'nama_ayah', 'pekerjaan_ayah',
            'nama_ibu', 'pekerjaan_ibu', 'nama_wali', 'pekerjaan_wali',
            'asal_sekolah', 'peserta_didik_id',
        ]);
    }
}
