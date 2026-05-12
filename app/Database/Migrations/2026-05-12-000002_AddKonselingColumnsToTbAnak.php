<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKonselingColumnsToTbAnak extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_anak')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('jenis_kelamin', 'tb_anak')) {
            $fields['jenis_kelamin'] = [
                'type'       => 'ENUM',
                'constraint' => ['L', 'P'],
                'null'       => true,
            ];
        }

        if (!$this->db->fieldExists('tanggal_lahir', 'tb_anak')) {
            $fields['tanggal_lahir'] = [
                'type' => 'DATE',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('umur_bulan', 'tb_anak')) {
            $fields['umur_bulan'] = [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
            ];
        }

        if (!$this->db->fieldExists('berat_badan', 'tb_anak')) {
            $fields['berat_badan'] = [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ];
        }

        if (!$this->db->fieldExists('tinggi_badan', 'tb_anak')) {
            $fields['tinggi_badan'] = [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => true,
            ];
        }

        if (!$this->db->fieldExists('created_at', 'tb_anak')) {
            $fields['created_at'] = [
                'type' => 'DATETIME',
                'null' => true,
            ];
        }

        if (!$this->db->fieldExists('updated_at', 'tb_anak')) {
            $fields['updated_at'] = [
                'type' => 'DATETIME',
                'null' => true,
            ];
        }

        if ($fields !== []) {
            $this->forge->addColumn('tb_anak', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_anak')) {
            return;
        }

        foreach (['updated_at', 'created_at', 'tinggi_badan', 'berat_badan', 'umur_bulan', 'tanggal_lahir', 'jenis_kelamin'] as $field) {
            if ($this->db->fieldExists($field, 'tb_anak')) {
                $this->forge->dropColumn('tb_anak', $field);
            }
        }
    }
}
