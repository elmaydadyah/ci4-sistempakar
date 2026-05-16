<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEditableCalculationReferences extends Migration
{
    public function up()
    {
        $this->createStandarAntropometri();
        $this->createNaiveBayesPrior();
        $this->createNaiveBayesLikelihood();
    }

    public function down()
    {
        $this->forge->dropTable('tb_naive_bayes_likelihood', true);
        $this->forge->dropTable('tb_naive_bayes_prior', true);
        $this->forge->dropTable('tb_standar_antropometri', true);
    }

    private function createStandarAntropometri(): void
    {
        if (!$this->db->tableExists('tb_standar_antropometri')) {
            $this->forge->addField([
                'id_standar' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'indikator' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'jenis_kelamin' => [
                    'type' => 'VARCHAR',
                    'constraint' => 1,
                ],
                'umur_bulan' => [
                    'type' => 'INT',
                    'constraint' => 3,
                    'null' => true,
                ],
                'tinggi_cm' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,2',
                    'null' => true,
                ],
                'median' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,2',
                ],
                'sd' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,2',
                ],
                'sumber' => [
                    'type' => 'VARCHAR',
                    'constraint' => 150,
                    'null' => true,
                ],
                'catatan' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id_standar', true);
            $this->forge->addKey(['indikator', 'jenis_kelamin', 'umur_bulan']);
            $this->forge->addKey(['indikator', 'jenis_kelamin', 'tinggi_cm']);
            $this->forge->createTable('tb_standar_antropometri', true);
        }

        if ($this->db->table('tb_standar_antropometri')->countAllResults() === 0) {
            $this->db->table('tb_standar_antropometri')->insertBatch($this->buildStandarRows());
        }
    }

    private function createNaiveBayesPrior(): void
    {
        if (!$this->db->tableExists('tb_naive_bayes_prior')) {
            $this->forge->addField([
                'id_prior' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'kelas' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'label' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'probabilitas' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,5',
                ],
                'rekomendasi' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id_prior', true);
            $this->forge->addUniqueKey('kelas');
            $this->forge->createTable('tb_naive_bayes_prior', true);
        }

        if ($this->db->table('tb_naive_bayes_prior')->countAllResults() === 0) {
            $this->db->table('tb_naive_bayes_prior')->insertBatch([
                [
                    'kelas' => 'H1',
                    'label' => 'Risiko rendah',
                    'probabilitas' => 0.50,
                    'rekomendasi' => 'Pertahankan pola makan bergizi seimbang, pemantauan rutin, imunisasi, dan stimulasi tumbuh kembang anak.',
                ],
                [
                    'kelas' => 'H2',
                    'label' => 'Risiko sedang',
                    'probabilitas' => 0.30,
                    'rekomendasi' => 'Perbaiki pola makan, pantau berat dan tinggi badan, serta lakukan konsultasi berkala dengan kader posyandu atau petugas kesehatan.',
                ],
                [
                    'kelas' => 'H3',
                    'label' => 'Risiko tinggi',
                    'probabilitas' => 0.20,
                    'rekomendasi' => 'Segera lakukan pemeriksaan lanjutan ke puskesmas atau tenaga kesehatan. Pantau asupan gizi, jadwal makan, dan pengukuran ulang secara rutin.',
                ],
            ]);
        }
    }

    private function createNaiveBayesLikelihood(): void
    {
        if (!$this->db->tableExists('tb_naive_bayes_likelihood')) {
            $this->forge->addField([
                'id_likelihood' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'indikator' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'kategori' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                ],
                'kelas' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'probabilitas' => [
                    'type' => 'DECIMAL',
                    'constraint' => '8,5',
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id_likelihood', true);
            $this->forge->addUniqueKey(['indikator', 'kategori', 'kelas']);
            $this->forge->createTable('tb_naive_bayes_likelihood', true);
        }

        if ($this->db->table('tb_naive_bayes_likelihood')->countAllResults() === 0) {
            $this->db->table('tb_naive_bayes_likelihood')->insertBatch($this->buildLikelihoodRows());
        }
    }

    private function buildStandarRows(): array
    {
        $rows = [];
        $now = date('Y-m-d H:i:s');

        foreach (['L', 'P'] as $gender) {
            $genderOffset = $gender === 'L' ? 0.2 : -0.2;

            for ($age = 0; $age <= 60; $age++) {
                $rows[] = [
                    'indikator' => 'BB/U',
                    'jenis_kelamin' => $gender,
                    'umur_bulan' => $age,
                    'tinggi_cm' => null,
                    'median' => $this->weightMedianByAge($age) + $genderOffset,
                    'sd' => round(1.05 + ($age * 0.025), 2),
                    'sumber' => 'Data awal sistem, dapat diedit sesuai standar rujukan',
                    'catatan' => 'Referensi awal BB/U untuk perhitungan Z-Score.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $rows[] = [
                    'indikator' => 'TB/U',
                    'jenis_kelamin' => $gender,
                    'umur_bulan' => $age,
                    'tinggi_cm' => null,
                    'median' => $this->heightMedianByAge($age) + $genderOffset,
                    'sd' => round(2.65 + ($age * 0.018), 2),
                    'sumber' => 'Data awal sistem, dapat diedit sesuai standar rujukan',
                    'catatan' => 'Referensi awal TB/U untuk perhitungan Z-Score.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            for ($height = 45; $height <= 120; $height++) {
                $rows[] = [
                    'indikator' => 'BB/TB',
                    'jenis_kelamin' => $gender,
                    'umur_bulan' => null,
                    'tinggi_cm' => $height,
                    'median' => round(max(2.4, -4.15 + (0.20 * $height)) + $genderOffset, 2),
                    'sd' => 1.15,
                    'sumber' => 'Data awal sistem, dapat diedit sesuai standar rujukan',
                    'catatan' => 'Referensi awal BB/TB untuk perhitungan Z-Score.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        return $rows;
    }

    private function buildLikelihoodRows(): array
    {
        $map = [
            'BB/U' => [
                'Berat badan sangat kurang' => ['H1' => 0.03, 'H2' => 0.24, 'H3' => 0.73],
                'Berat badan kurang' => ['H1' => 0.10, 'H2' => 0.65, 'H3' => 0.25],
                'Berat badan normal' => ['H1' => 0.84, 'H2' => 0.13, 'H3' => 0.03],
                'Risiko berat badan lebih' => ['H1' => 0.66, 'H2' => 0.27, 'H3' => 0.07],
            ],
            'TB/U' => [
                'Sangat pendek' => ['H1' => 0.02, 'H2' => 0.18, 'H3' => 0.80],
                'Pendek' => ['H1' => 0.08, 'H2' => 0.68, 'H3' => 0.24],
                'Normal' => ['H1' => 0.86, 'H2' => 0.11, 'H3' => 0.03],
                'Tinggi' => ['H1' => 0.82, 'H2' => 0.14, 'H3' => 0.04],
            ],
            'BB/TB' => [
                'Gizi buruk' => ['H1' => 0.03, 'H2' => 0.22, 'H3' => 0.75],
                'Gizi kurang' => ['H1' => 0.09, 'H2' => 0.66, 'H3' => 0.25],
                'Gizi baik' => ['H1' => 0.86, 'H2' => 0.11, 'H3' => 0.03],
                'Gizi lebih' => ['H1' => 0.65, 'H2' => 0.27, 'H3' => 0.08],
            ],
        ];

        $rows = [];
        $now = date('Y-m-d H:i:s');

        foreach ($map as $indicator => $categories) {
            foreach ($categories as $category => $classes) {
                foreach ($classes as $class => $probability) {
                    $rows[] = [
                        'indikator' => $indicator,
                        'kategori' => $category,
                        'kelas' => $class,
                        'probabilitas' => $probability,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        return $rows;
    }

    private function weightMedianByAge(int $age): float
    {
        if ($age <= 6) {
            return round(3.3 + (0.62 * $age), 2);
        }

        if ($age <= 12) {
            return round(7.0 + (0.38 * ($age - 6)), 2);
        }

        if ($age <= 24) {
            return round(9.3 + (0.20 * ($age - 12)), 2);
        }

        return round(11.7 + (0.16 * ($age - 24)), 2);
    }

    private function heightMedianByAge(int $age): float
    {
        if ($age <= 12) {
            return round(49.9 + (2.10 * $age), 2);
        }

        if ($age <= 24) {
            return round(75.1 + (1.00 * ($age - 12)), 2);
        }

        return round(87.1 + (0.63 * ($age - 24)), 2);
    }
}
