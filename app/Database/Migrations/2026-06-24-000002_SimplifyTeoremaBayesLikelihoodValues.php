<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SimplifyTeoremaBayesLikelihoodValues extends Migration
{
    public function up()
    {
        $this->applyRows($this->simplifiedRows());
    }

    public function down()
    {
        $this->applyRows($this->originalRows());
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

    private function simplifiedRows(): array
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

    private function originalRows(): array
    {
        return $this->buildRows([
            'BB/U' => [
                'Berat badan sangat kurang' => [0.73, 0.24, 0.03],
                'Berat badan kurang' => [0.25, 0.65, 0.10],
                'Berat badan normal' => [0.03, 0.13, 0.84],
                'Risiko berat badan lebih' => [0.07, 0.27, 0.66],
            ],
            'TB/U' => [
                'Sangat pendek' => [0.80, 0.18, 0.02],
                'Pendek' => [0.24, 0.68, 0.08],
                'Normal' => [0.03, 0.11, 0.86],
                'Tinggi' => [0.04, 0.14, 0.82],
            ],
            'BB/TB' => [
                'Gizi buruk' => [0.75, 0.22, 0.03],
                'Gizi kurang' => [0.25, 0.66, 0.09],
                'Gizi baik' => [0.03, 0.11, 0.86],
                'Gizi lebih' => [0.08, 0.27, 0.65],
                'Berisiko gizi lebih' => [0.06, 0.22, 0.72],
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
