<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdjustGoodNutritionLikelihoodForStunting extends Migration
{
    public function up()
    {
        $this->applyRows([
            'H1' => 0.20,
            'H2' => 0.30,
            'H3' => 1.00,
        ]);
    }

    public function down()
    {
        $this->applyRows([
            'H1' => 0.05,
            'H2' => 0.20,
            'H3' => 1.00,
        ]);
    }

    private function applyRows(array $values): void
    {
        $table = $this->getLikelihoodTable();
        if ($table === null) {
            return;
        }

        foreach ($values as $class => $probability) {
            $this->db->table($table)
                ->where('indikator', 'BB/TB')
                ->where('kategori', 'Gizi baik')
                ->where('kelas', $class)
                ->update([
                    'probabilitas' => $probability,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }

    private function getLikelihoodTable(): ?string
    {
        foreach (['tb_teorema_bayes_likelihood', 'tb_naive_bayes_likelihood'] as $table) {
            if ($this->db->tableExists($table)) {
                return $table;
            }
        }

        return null;
    }
}
