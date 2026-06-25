<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateG12GejalaLabel extends Migration
{
    private const NEW_LABEL = 'Anak memiliki Berat Badan cenderung tetap/menurun';
    private const OLD_LABEL = 'Kepala lebih besar dibanding badan';

    public function up()
    {
        $this->updateLabel(self::NEW_LABEL);
    }

    public function down()
    {
        $this->updateLabel(self::OLD_LABEL);
    }

    private function updateLabel(string $label): void
    {
        if (!$this->db->tableExists('tb_gejala')) {
            return;
        }

        $payload = ['nama_gejala' => $label];

        if ($this->db->fieldExists('updated_at', 'tb_gejala')) {
            $payload['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->db->table('tb_gejala')
            ->where('kode_gejala', 'G12')
            ->update($payload);
    }
}
