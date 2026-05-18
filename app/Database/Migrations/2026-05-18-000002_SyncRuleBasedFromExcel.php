<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncRuleBasedFromExcel extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_rule_based')) {
            return;
        }

        $fields = $this->db->getFieldNames('tb_rule_based');

        if (!in_array('kode_hipotesis', $fields, true)) {
            $this->forge->addColumn('tb_rule_based', [
                'kode_hipotesis' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                    'null' => true,
                    'after' => 'nama_rule',
                ],
            ]);
        }

        if (!in_array('kode_gejala', $fields, true)) {
            $this->forge->addColumn('tb_rule_based', [
                'kode_gejala' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                    'null' => true,
                    'after' => 'kode_hipotesis',
                ],
            ]);
        }

        $fields = $this->db->getFieldNames('tb_rule_based');
        $legacyColumns = [];

        if (in_array('indikator', $fields, true)) {
            $legacyColumns['indikator'] = [
                'name' => 'indikator',
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ];
        }

        if (in_array('kategori_hasil', $fields, true)) {
            $legacyColumns['kategori_hasil'] = [
                'name' => 'kategori_hasil',
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ];
        }

        if ($legacyColumns !== []) {
            $this->forge->modifyColumn('tb_rule_based', $legacyColumns);
        }

        $this->seedExcelRules();
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_rule_based')) {
            return;
        }

        $this->db->table('tb_rule_based')
            ->where('catatan', 'Import dari file Rule Base.xlsx.')
            ->delete();
    }

    private function seedExcelRules(): void
    {
        $now = date('Y-m-d H:i:s');
        $rows = $this->getExcelRuleRows();

        foreach ($rows as $index => $row) {
            [$hipotesis, $gejala] = $row;
            $kodeRule = 'RB' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT);
            $existing = $this->db->table('tb_rule_based')
                ->groupStart()
                    ->where('kode_rule', $kodeRule)
                    ->orGroupStart()
                        ->where('kode_hipotesis', $hipotesis)
                        ->where('kode_gejala', $gejala)
                    ->groupEnd()
                ->groupEnd()
                ->get()
                ->getRowArray();

            $payload = [
                'kode_rule' => $kodeRule,
                'nama_rule' => $hipotesis . ' -> ' . $gejala,
                'kode_hipotesis' => $hipotesis,
                'kode_gejala' => $gejala,
                'aktif' => 1,
                'urutan' => ($index + 1) * 10,
                'catatan' => 'Import dari file Rule Base.xlsx.',
                'updated_at' => $now,
            ];

            if ($this->db->fieldExists('indikator', 'tb_rule_based')) {
                $payload['indikator'] = null;
            }

            if ($this->db->fieldExists('kategori_hasil', 'tb_rule_based')) {
                $payload['kategori_hasil'] = null;
            }

            if ($existing) {
                $this->db->table('tb_rule_based')
                    ->where('id_rule', (int) $existing['id_rule'])
                    ->update($payload);
                continue;
            }

            $payload['created_at'] = $now;
            $this->db->table('tb_rule_based')->insert($payload);
        }
    }

    private function getExcelRuleRows(): array
    {
        return [
            ['H1', 'G02'],
            ['H1', 'G11'],
            ['H1', 'G01'],
            ['H1', 'G04'],
            ['H1', 'G06'],
            ['H1', 'G09'],
            ['H1', 'G10'],
            ['H1', 'G15'],
            ['H1', 'G17'],
            ['H1', 'G18'],
            ['H2', 'G03'],
            ['H2', 'G05'],
            ['H2', 'G07'],
            ['H2', 'G08'],
            ['H2', 'G13'],
            ['H2', 'G16'],
            ['H2', 'G19'],
            ['H2', 'G20'],
            ['H3', 'G03'],
            ['H3', 'G07'],
            ['H3', 'G13'],
            ['H3', 'G14'],
            ['H3', 'G19'],
            ['H3', 'G20'],
        ];
    }
}
