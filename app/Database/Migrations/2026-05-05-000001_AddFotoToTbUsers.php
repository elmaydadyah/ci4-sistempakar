<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFotoToTbUsers extends Migration
{
    public function up()
    {
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
        $this->forge->dropColumn('tb_users', 'foto');
    }
}
