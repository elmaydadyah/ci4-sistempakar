<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbRuleBased extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_rule_based')) {
            $this->forge->addField([
                'id_rule' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'kode_rule' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'nama_rule' => [
                    'type' => 'VARCHAR',
                    'constraint' => 150,
                    'null' => true,
                ],
                'kode_hipotesis' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'kode_gejala' => [
                    'type' => 'VARCHAR',
                    'constraint' => 20,
                ],
                'aktif' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 1,
                ],
                'urutan' => [
                    'type' => 'INT',
                    'constraint' => 5,
                    'default' => 0,
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
            $this->forge->addKey('id_rule', true);
            $this->forge->addUniqueKey('kode_rule');
            $this->forge->addUniqueKey(['kode_hipotesis', 'kode_gejala'], 'uq_rule_hipotesis_gejala');
            $this->forge->addKey(['kode_hipotesis', 'aktif', 'urutan']);
            $this->forge->createTable('tb_rule_based', true);
        }

        if ($this->db->table('tb_rule_based')->countAllResults() === 0) {
            $this->seedRuleBased();
        }
    }

    public function down()
    {
        $this->forge->dropTable('tb_rule_based', true);
    }

    private function seedRuleBased(): void
    {
        $now = date('Y-m-d H:i:s');
        $rows = $this->getExcelRuleRows();

        $payload = array_map(static fn ($row, $index) => [
            'kode_rule' => 'RB' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT),
            'nama_rule' => $row[0] . ' -> ' . $row[1],
            'kode_hipotesis' => $row[0],
            'kode_gejala' => $row[1],
            'aktif' => 1,
            'urutan' => ($index + 1) * 10,
            'catatan' => 'Import dari file Rule Base.xlsx.',
            'created_at' => $now,
            'updated_at' => $now,
        ], $rows, array_keys($rows));

        $this->db->table('tb_rule_based')->insertBatch($payload);
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
