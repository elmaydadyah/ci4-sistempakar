<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAddressFieldsToConsultation extends Migration
{
    public function up()
    {
        $this->addAddressFields('tb_anak', 'alamat');
        $this->addAddressFields('tb_hasil_diagnosa', 'alamat');
    }

    public function down()
    {
        $this->dropAddressFields('tb_anak');
        $this->dropAddressFields('tb_hasil_diagnosa');
    }

    private function addAddressFields(string $table, string $after): void
    {
        if (!$this->db->tableExists($table)) {
            return;
        }

        $fields = [
            'rt' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => $after,
            ],
            'rw' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => 'rt',
            ],
            'desa' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'rw',
            ],
            'kelurahan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'desa',
            ],
            'kecamatan' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'kelurahan',
            ],
        ];

        if (!$this->db->fieldExists('alamat', $table)) {
            $fields = ['alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ]] + $fields;
        }

        $newFields = [];
        foreach ($fields as $name => $definition) {
            if (!$this->db->fieldExists($name, $table)) {
                $newFields[$name] = $definition;
            }
        }

        if ($newFields !== []) {
            $this->forge->addColumn($table, $newFields);
        }
    }

    private function dropAddressFields(string $table): void
    {
        if (!$this->db->tableExists($table)) {
            return;
        }

        foreach (['rt', 'rw', 'desa', 'kelurahan', 'kecamatan'] as $field) {
            if ($this->db->fieldExists($field, $table)) {
                $this->forge->dropColumn($table, $field);
            }
        }
    }
}
