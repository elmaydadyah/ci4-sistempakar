<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncNewGejalaProbabilityAndRuleBase extends Migration
{
    public function up()
    {
        $this->syncGejala();
        $this->syncNilaiProbabilitas();
        $this->syncRuleBased();
    }

    public function down()
    {
        // Data master dikembalikan melalui backup/seed lama bila diperlukan.
    }

    private function syncGejala(): void
    {
        if (!$this->db->tableExists('tb_gejala')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($this->gejalaRows() as $kodeGejala => $namaGejala) {
            $existing = $this->db->table('tb_gejala')
                ->select('id_gejala')
                ->where('kode_gejala', $kodeGejala)
                ->get()
                ->getRowArray();

            $payload = [
                'kode_gejala' => $kodeGejala,
                'nama_gejala' => $namaGejala,
            ];

            if ($this->db->fieldExists('updated_at', 'tb_gejala')) {
                $payload['updated_at'] = $now;
            }

            if ($existing) {
                $this->db->table('tb_gejala')
                    ->where('id_gejala', (int) $existing['id_gejala'])
                    ->update($payload);
                continue;
            }

            if ($this->db->fieldExists('created_at', 'tb_gejala')) {
                $payload['created_at'] = $now;
            }

            $this->db->table('tb_gejala')->insert($payload);
        }
    }

    private function syncNilaiProbabilitas(): void
    {
        if (!$this->db->tableExists('tb_gejala') || !$this->db->tableExists('tb_nilai_probabilitas')) {
            return;
        }

        $rows = $this->nilaiProbabilitasRows();
        $gejalaRows = $this->db->table('tb_gejala')
            ->select('id_gejala, kode_gejala')
            ->whereIn('kode_gejala', array_keys($rows))
            ->get()
            ->getResultArray();

        $gejalaByKode = [];
        foreach ($gejalaRows as $gejala) {
            $gejalaByKode[(string) $gejala['kode_gejala']] = (int) $gejala['id_gejala'];
        }

        if ($gejalaByKode === []) {
            return;
        }

        $this->db->table('tb_nilai_probabilitas')
            ->whereNotIn('id_gejala', array_values($gejalaByKode))
            ->delete();

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

    private function syncRuleBased(): void
    {
        if (!$this->db->tableExists('tb_rule_based')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $payload = [];
        foreach ($this->ruleBaseRows() as $index => $row) {
            [$kodeHipotesis, $kodeGejala] = $row;
            $payload[] = [
                'kode_rule' => 'RB' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
                'nama_rule' => $kodeHipotesis . ' -> ' . $kodeGejala,
                'kode_hipotesis' => $kodeHipotesis,
                'kode_gejala' => $kodeGejala,
                'aktif' => 1,
                'urutan' => ($index + 1) * 10,
                'catatan' => 'Import dari file Rule Base.xlsx - update 2026-06-04.',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $this->db->table('tb_rule_based')->truncate();
        if ($payload !== []) {
            $this->db->table('tb_rule_based')->insertBatch($payload);
        }
    }

    private function gejalaRows(): array
    {
        return [
            'G01' => 'Berat badan cenderung kurang (Tidak sesuai dengan anak seusianya)',
            'G02' => 'Tinggi badan lebih pendek / rendah dari standar anak seusianya',
            'G03' => 'Kurang aktif',
            'G04' => 'Daya tahan tubuh rendah (anak sering sakit)',
            'G05' => 'Mengalami keterlambatan dalam berbicara',
            'G06' => 'Lambatnya perkembangan keterampilan fisik, seperti berguling, duduk, berdiri, dan berjalan',
            'G07' => 'Susah fokus',
            'G08' => 'Gigi Susu dan Gigi Permanen terlambat tumbuh',
            'G09' => 'Anak tidak mendapat pengukuran Tinggi badan dan berat badan rutin (8x Setahun) di Posyandu/Klinik bidan dan dicatat di buku KMS',
            'G10' => 'Nafsu makan berkurang',
            'G11' => 'Kekurangan Gizi',
            'G12' => 'Kepala lebih besar dibanding badan',
            'G13' => 'Anak tidak mendapatkan imunisasi dasar lengkap (HB 0, BCG, Polio, DPT, dan Campak)',
            'G14' => 'Anak tidak rutin mendapatkan obat kecacingan (2x Setahun)',
            'G15' => 'Ibu memiliki HB < 11 selama masa kehamilan',
            'G16' => 'Selama hamil Ibu mengalami mual muntah (morning sickness) setelah trimester 1 atau lebih dari trimester 1',
            'G17' => 'Ibu sering merasa lemas, pusing, dan mudah lelah saat hamil',
            'G18' => 'Ibu mengalami penurunan atau kenaikan berat badan tidak normal saat hamil',
            'G19' => 'Ibu memiliki lingkar lengan atas < 23,5',
            'G20' => 'Tidak memiliki akses jamban sehat (jenis leher angsa / memiliki septic tank)',
            'G21' => 'Tidak memiliki akses air bersih di rumah',
        ];
    }

    private function nilaiProbabilitasRows(): array
    {
        return [
            'G01' => ['H1' => 0.9, 'H2' => 0.7, 'H3' => 0.2],
            'G02' => ['H1' => 1.0, 'H2' => 0.7, 'H3' => 0.1],
            'G03' => ['H1' => 0.7, 'H2' => 0.6, 'H3' => 0.3],
            'G04' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.2],
            'G05' => ['H1' => 0.6, 'H2' => 0.7, 'H3' => 0.3],
            'G06' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.2],
            'G07' => ['H1' => 0.5, 'H2' => 0.6, 'H3' => 0.3],
            'G08' => ['H1' => 0.5, 'H2' => 0.6, 'H3' => 0.3],
            'G09' => ['H1' => 0.6, 'H2' => 0.8, 'H3' => 0.2],
            'G10' => ['H1' => 0.7, 'H2' => 0.6, 'H3' => 0.3],
            'G11' => ['H1' => 0.9, 'H2' => 0.7, 'H3' => 0.2],
            'G12' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.2],
            'G13' => ['H1' => 0.6, 'H2' => 0.7, 'H3' => 0.3],
            'G14' => ['H1' => 0.5, 'H2' => 0.7, 'H3' => 0.3],
            'G15' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.2],
            'G16' => ['H1' => 0.5, 'H2' => 0.6, 'H3' => 0.3],
            'G17' => ['H1' => 0.5, 'H2' => 0.6, 'H3' => 0.3],
            'G18' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.2],
            'G19' => ['H1' => 0.8, 'H2' => 0.6, 'H3' => 0.2],
            'G20' => ['H1' => 0.6, 'H2' => 0.7, 'H3' => 0.3],
            'G21' => ['H1' => 0.6, 'H2' => 0.7, 'H3' => 0.3],
        ];
    }

    private function ruleBaseRows(): array
    {
        return [
            ['H1', 'G01'], ['H1', 'G02'], ['H1', 'G03'], ['H1', 'G04'], ['H1', 'G05'],
            ['H1', 'G06'], ['H1', 'G09'], ['H1', 'G10'], ['H1', 'G11'], ['H1', 'G12'],
            ['H1', 'G13'], ['H1', 'G15'], ['H1', 'G18'], ['H1', 'G19'],
            ['H2', 'G01'], ['H2', 'G03'], ['H2', 'G04'], ['H2', 'G05'], ['H2', 'G06'],
            ['H2', 'G07'], ['H2', 'G08'], ['H2', 'G09'], ['H2', 'G10'], ['H2', 'G13'],
            ['H2', 'G14'], ['H2', 'G16'], ['H2', 'G17'], ['H2', 'G20'], ['H2', 'G21'],
            ['H3', 'G07'], ['H3', 'G08'], ['H3', 'G14'], ['H3', 'G16'], ['H3', 'G17'],
            ['H3', 'G20'], ['H3', 'G21'],
        ];
    }
}
