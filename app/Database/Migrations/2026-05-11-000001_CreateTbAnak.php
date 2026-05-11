<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbAnak extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_anak' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_anak' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'jenis_kelamin' => [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'umur_bulan' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
            ],
            'berat_badan' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ],
            'tinggi_badan' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ],
            'nama_ortu' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_anak', true);
        $this->forge->createTable('tb_anak', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_anak', true);
    }
}
