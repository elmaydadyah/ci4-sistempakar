<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AdjustSevereZScoreLikelihoodWeights extends Migration
{
    public function up()
    {
        $this->applyRows($this->adjustedRows());
    }

    public function down()
    {
        $this->applyRows($this->previousRows());
    }

    private function applyRows(array $rows): void
    {
        $table = $this->getLikelihoodTable();
        if ($table === null) {
            return;
        }

        foreach ($rows as $row) {
            $this->db->table($table)
                ->where('indikator', $row['indikator'])
                ->where('kategori', $row['kategori'])
                ->where('kelas', $row['kelas'])
                ->update([
                    'probabilitas' => $row['probabilitas'],
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

    private function adjustedRows(): array
    {
        return $this->buildRows([
            'BB/U' => [
                'Berat badan sangat kurang' => [1.00, 0.40, 0.10],
            ],
            'TB/U' => [
                'Sangat pendek' => [1.00, 0.40, 0.10],
            ],
            'BB/TB' => [
                'Gizi buruk' => [1.00, 0.40, 0.10],
            ],
        ]);
    }

    private function previousRows(): array
    {
        return $this->buildRows([
            'BB/U' => [
                'Berat badan sangat kurang' => [1.00, 0.10, 0.01],
            ],
            'TB/U' => [
                'Sangat pendek' => [1.00, 0.10, 0.01],
            ],
            'BB/TB' => [
                'Gizi buruk' => [1.00, 0.10, 0.01],
            ],
        ]);
    }

    private function buildRows(array $groups): array
    {
        $rows = [];
        foreach ($groups as $indikator => $categories) {
            foreach ($categories as $kategori => $values) {
                foreach (['H1', 'H2', 'H3'] as $index => $kelas) {
                    $rows[] = [
                        'indikator' => $indikator,
                        'kategori' => $kategori,
                        'kelas' => $kelas,
                        'probabilitas' => $values[$index],
                    ];
                }
            }
        }

        return $rows;
    }
}
