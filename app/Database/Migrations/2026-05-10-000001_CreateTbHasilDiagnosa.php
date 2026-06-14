<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbHasilDiagnosa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_hasil_diagnosa' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'umur' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'id_kasus' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'nama_kasus' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'persentase' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],
            'jumlah_gejala' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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

        $this->forge->addKey('id_hasil_diagnosa', true);
        $this->forge->addKey('created_at');
        $this->forge->addKey('id_kasus');
        $this->forge->createTable('tb_hasil_diagnosa', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_hasil_diagnosa', true);
    }
}
