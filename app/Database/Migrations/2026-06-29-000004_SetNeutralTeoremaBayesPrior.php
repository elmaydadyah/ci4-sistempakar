<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetNeutralTeoremaBayesPrior extends Migration
{
    public function up()
    {
        $this->applyRows([
            'H1' => 0.33333,
            'H2' => 0.33333,
            'H3' => 0.33334,
        ]);
    }

    public function down()
    {
        $this->applyRows([
            'H1' => 0.20,
            'H2' => 0.30,
            'H3' => 0.50,
        ]);
    }

    private function applyRows(array $values): void
    {
        $table = $this->getPriorTable();
        if ($table === null) {
            return;
        }

        foreach ($values as $class => $probability) {
            $this->db->table($table)
                ->where('kelas', $class)
                ->update([
                    'probabilitas' => $probability,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }

    private function getPriorTable(): ?string
    {
        foreach (['tb_teorema_bayes_prior', 'tb_naive_bayes_prior'] as $table) {
            if ($this->db->tableExists($table)) {
                return $table;
            }
        }

        return null;
    }
}
