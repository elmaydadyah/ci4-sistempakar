<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StrengthenZScoreLikelihoodWeights extends Migration
{
    public function up()
    {
        $this->applyRows($this->strengthenedRows());
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

    private function strengthenedRows(): array
    {
        return $this->buildRows([
            'BB/U' => [
                'Berat badan sangat kurang' => [1.00, 0.10, 0.01],
                'Berat badan kurang' => [0.65, 1.00, 0.10],
                'Berat badan normal' => [0.05, 0.20, 1.00],
                'Risiko berat badan lebih' => [0.05, 0.20, 1.00],
            ],
            'TB/U' => [
                'Sangat pendek' => [1.00, 0.10, 0.01],
                'Pendek' => [0.65, 1.00, 0.10],
                'Normal' => [0.05, 0.20, 1.00],
                'Tinggi' => [0.05, 0.20, 1.00],
            ],
            'BB/TB' => [
                'Gizi buruk' => [1.00, 0.10, 0.01],
                'Gizi kurang' => [0.65, 1.00, 0.10],
                'Gizi baik' => [0.05, 0.20, 1.00],
                'Gizi lebih' => [0.05, 0.20, 1.00],
                'Berisiko gizi lebih' => [0.05, 0.20, 1.00],
            ],
        ]);
    }

    private function previousRows(): array
    {
        return $this->buildRows([
            'BB/U' => [
                'Berat badan sangat kurang' => [0.80, 0.20, 0.05],
                'Berat badan kurang' => [0.30, 0.70, 0.10],
                'Berat badan normal' => [0.05, 0.20, 0.80],
                'Risiko berat badan lebih' => [0.05, 0.20, 0.80],
            ],
            'TB/U' => [
                'Sangat pendek' => [0.80, 0.20, 0.05],
                'Pendek' => [0.30, 0.70, 0.10],
                'Normal' => [0.05, 0.20, 0.80],
                'Tinggi' => [0.05, 0.20, 0.80],
            ],
            'BB/TB' => [
                'Gizi buruk' => [0.80, 0.20, 0.05],
                'Gizi kurang' => [0.30, 0.70, 0.10],
                'Gizi baik' => [0.05, 0.20, 0.80],
                'Gizi lebih' => [0.05, 0.20, 0.80],
                'Berisiko gizi lebih' => [0.05, 0.20, 0.80],
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
