<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeskripsiToTbKasus extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_kasus') || $this->db->fieldExists('deskripsi', 'tb_kasus')) {
            return;
        }

        $this->forge->addColumn('tb_kasus', [
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'nama_kasus',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('tb_kasus') && $this->db->fieldExists('deskripsi', 'tb_kasus')) {
            $this->forge->dropColumn('tb_kasus', 'deskripsi');
        }
    }
}
