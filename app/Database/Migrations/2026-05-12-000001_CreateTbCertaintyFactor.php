<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbCertaintyFactor extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_cf' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_gejala' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'bobot_cf' => [
                'type'       => 'DECIMAL',
                'constraint' => '4,2',
                'default'    => 0,
            ],
            'keterangan' => [
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

        $this->forge->addKey('id_cf', true);
        $this->forge->addUniqueKey('id_gejala');
        $this->forge->createTable('tb_certainty_factor', true);
    }

    public function down()
    {
        $this->forge->dropTable('tb_certainty_factor', true);
    }
}
