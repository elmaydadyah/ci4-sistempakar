<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterTbHasilDiagnosaPersentaseDecimal extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_hasil_diagnosa') || !$this->db->fieldExists('persentase', 'tb_hasil_diagnosa')) {
            return;
        }

        $this->forge->modifyColumn('tb_hasil_diagnosa', [
            'persentase' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0,
            ],
        ]);
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_hasil_diagnosa') || !$this->db->fieldExists('persentase', 'tb_hasil_diagnosa')) {
            return;
        }

        $this->forge->modifyColumn('tb_hasil_diagnosa', [
            'persentase' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ]);
    }
}
