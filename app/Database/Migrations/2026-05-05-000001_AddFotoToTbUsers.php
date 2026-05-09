<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToTbUsers extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('foto', 'tb_users')) {
            return;
        }

        $this->forge->addColumn('tb_users', [
            'foto' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'role',
            ],
        ]);
    }

    public function down()
    {
        if (!$this->db->fieldExists('foto', 'tb_users')) {
            return;
        }

        $this->forge->dropColumn('tb_users', 'foto');
    }
}
