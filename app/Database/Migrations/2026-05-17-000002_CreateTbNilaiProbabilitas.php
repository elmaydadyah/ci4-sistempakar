<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbNilaiProbabilitas extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_nilai_probabilitas')) {
            $this->forge->addField([
                'id_nilai_probabilitas' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'id_gejala' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                ],
                'kode_hipotesis' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'nilai_probabilitas' => [
                    'type' => 'DECIMAL',
                    'constraint' => '4,2',
                    'default' => 0,
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

            $this->forge->addKey('id_nilai_probabilitas', true);
            $this->forge->addKey('id_gejala');
            $this->forge->addKey('kode_hipotesis');
            $this->forge->addUniqueKey(['id_gejala', 'kode_hipotesis'], 'uq_nilai_probabilitas_gejala_hipotesis');
            $this->forge->createTable('tb_nilai_probabilitas', true);
        }

        $this->seedNilaiProbabilitas();
    }

    public function down()
    {
        $this->forge->dropTable('tb_nilai_probabilitas', true);
    }

    private function seedNilaiProbabilitas(): void
    {
        if (!$this->db->tableExists('tb_nilai_probabilitas') || !$this->db->tableExists('tb_gejala')) {
            return;
        }

        $rows = [
            'G01' => ['H1' => 0.9, 'H2' => 0.7, 'H3' => 0.3],
            'G02' => ['H1' => 1.0, 'H2' => 0.8, 'H3' => 0.2],
            'G03' => ['H1' => 0.6, 'H2' => 0.5, 'H3' => 0.4],
            'G04' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.3],
            'G05' => ['H1' => 0.7, 'H2' => 0.5, 'H3' => 0.3],
            'G06' => ['H1' => 0.9, 'H2' => 0.7, 'H3' => 0.3],
            'G07' => ['H1' => 0.6, 'H2' => 0.5, 'H3' => 0.4],
            'G08' => ['H1' => 0.7, 'H2' => 0.5, 'H3' => 0.3],
            'G09' => ['H1' => 0.8, 'H2' => 0.7, 'H3' => 0.4],
            'G10' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.4],
            'G11' => ['H1' => 1.0, 'H2' => 0.8, 'H3' => 0.2],
            'G12' => ['H1' => 0.4, 'H2' => 0.4, 'H3' => 0.3],
            'G13' => ['H1' => 0.6, 'H2' => 0.5, 'H3' => 0.4],
            'G14' => ['H1' => 0.3, 'H2' => 0.3, 'H3' => 0.2],
            'G15' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.3],
            'G16' => ['H1' => 0.7, 'H2' => 0.6, 'H3' => 0.4],
            'G17' => ['H1' => 0.8, 'H2' => 0.7, 'H3' => 0.4],
            'G18' => ['H1' => 0.9, 'H2' => 0.8, 'H3' => 0.3],
            'G19' => ['H1' => 0.6, 'H2' => 0.5, 'H3' => 0.4],
            'G20' => ['H1' => 0.6, 'H2' => 0.4, 'H3' => 0.4],
        ];

        $gejalaRows = $this->db->table('tb_gejala')
            ->select('id_gejala, kode_gejala')
            ->whereIn('kode_gejala', array_keys($rows))
            ->get()
            ->getResultArray();

        $gejalaByKode = [];
        foreach ($gejalaRows as $gejala) {
            $gejalaByKode[(string) $gejala['kode_gejala']] = (int) $gejala['id_gejala'];
        }

        $now = date('Y-m-d H:i:s');
        foreach ($rows as $kodeGejala => $nilaiByHipotesis) {
            if (!isset($gejalaByKode[$kodeGejala])) {
                continue;
            }

            foreach ($nilaiByHipotesis as $kodeHipotesis => $nilai) {
                $payload = [
                    'id_gejala' => $gejalaByKode[$kodeGejala],
                    'kode_hipotesis' => $kodeHipotesis,
                    'nilai_probabilitas' => $nilai,
                    'updated_at' => $now,
                ];

                $existing = $this->db->table('tb_nilai_probabilitas')
                    ->select('id_nilai_probabilitas')
                    ->where('id_gejala', $payload['id_gejala'])
                    ->where('kode_hipotesis', $kodeHipotesis)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    $this->db->table('tb_nilai_probabilitas')
                        ->where('id_nilai_probabilitas', (int) $existing['id_nilai_probabilitas'])
                        ->update($payload);
                    continue;
                }

                $payload['created_at'] = $now;
                $this->db->table('tb_nilai_probabilitas')->insert($payload);
            }
        }
    }
}
