<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNaiveBayesConsultationFields extends Migration
{
    public function up()
    {
        $this->addAnakFields();
        $this->addHasilDiagnosaFields();
    }

    public function down()
    {
        $this->dropFields('tb_hasil_diagnosa', [
            'id_anak',
            'nik',
            'jenis_kelamin',
            'tanggal_lahir',
            'berat_badan',
            'tinggi_badan',
            'lingkar_lengan',
            'lingkar_kepala',
            'riwayat_kehamilan',
            'pola_makan',
            'tempat_tinggal',
            'zs_bb_u',
            'kategori_bb_u',
            'zs_tb_u',
            'kategori_tb_u',
            'zs_bb_tb',
            'kategori_bb_tb',
            'gejala_zscore',
            'kelas_hasil',
            'probabilitas_prior',
            'probabilitas_likelihood',
            'probabilitas_posterior',
            'rekomendasi',
        ]);

        $this->dropFields('tb_anak', [
            'nik',
            'lingkar_lengan',
            'lingkar_kepala',
            'riwayat_kehamilan',
            'pola_makan',
            'tempat_tinggal',
            'zs_bb_u',
            'kategori_bb_u',
            'zs_tb_u',
            'kategori_tb_u',
            'zs_bb_tb',
            'kategori_bb_tb',
            'gejala_zscore',
        ]);
    }

    private function addAnakFields(): void
    {
        if (!$this->db->tableExists('tb_anak')) {
            return;
        }

        $this->addFields('tb_anak', [
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
                'after' => 'nama_anak',
            ],
            'lingkar_lengan' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'tinggi_badan',
            ],
            'lingkar_kepala' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'lingkar_lengan',
            ],
            'riwayat_kehamilan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'alamat',
            ],
            'pola_makan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'riwayat_kehamilan',
            ],
            'tempat_tinggal' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'pola_makan',
            ],
            'zs_bb_u' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'tempat_tinggal',
            ],
            'kategori_bb_u' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'zs_bb_u',
            ],
            'zs_tb_u' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'kategori_bb_u',
            ],
            'kategori_tb_u' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'zs_tb_u',
            ],
            'zs_bb_tb' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'kategori_tb_u',
            ],
            'kategori_bb_tb' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'zs_bb_tb',
            ],
            'gejala_zscore' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'kategori_bb_tb',
            ],
        ]);
    }

    private function addHasilDiagnosaFields(): void
    {
        if (!$this->db->tableExists('tb_hasil_diagnosa')) {
            return;
        }

        $this->addFields('tb_hasil_diagnosa', [
            'id_anak' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id_hasil_diagnosa',
            ],
            'nik' => [
                'type' => 'VARCHAR',
                'constraint' => 32,
                'null' => true,
                'after' => 'nama',
            ],
            'jenis_kelamin' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'null' => true,
                'after' => 'nik',
            ],
            'tanggal_lahir' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'jenis_kelamin',
            ],
            'berat_badan' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'umur',
            ],
            'tinggi_badan' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'berat_badan',
            ],
            'lingkar_lengan' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'tinggi_badan',
            ],
            'lingkar_kepala' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'lingkar_lengan',
            ],
            'riwayat_kehamilan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'lingkar_kepala',
            ],
            'pola_makan' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'riwayat_kehamilan',
            ],
            'tempat_tinggal' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => true,
                'after' => 'pola_makan',
            ],
            'zs_bb_u' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'tempat_tinggal',
            ],
            'kategori_bb_u' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'zs_bb_u',
            ],
            'zs_tb_u' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'kategori_bb_u',
            ],
            'kategori_tb_u' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'zs_tb_u',
            ],
            'zs_bb_tb' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'null' => true,
                'after' => 'kategori_tb_u',
            ],
            'kategori_bb_tb' => [
                'type' => 'VARCHAR',
                'constraint' => 80,
                'null' => true,
                'after' => 'zs_bb_tb',
            ],
            'gejala_zscore' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'kategori_bb_tb',
            ],
            'kelas_hasil' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'gejala_zscore',
            ],
            'probabilitas_prior' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'kelas_hasil',
            ],
            'probabilitas_likelihood' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'probabilitas_prior',
            ],
            'probabilitas_posterior' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'probabilitas_likelihood',
            ],
            'rekomendasi' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'probabilitas_posterior',
            ],
        ]);
    }

    private function addFields(string $table, array $fields): void
    {
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

    private function dropFields(string $table, array $fields): void
    {
        if (!$this->db->tableExists($table)) {
            return;
        }

        foreach ($fields as $field) {
            if ($this->db->fieldExists($field, $table)) {
                $this->forge->dropColumn($table, $field);
            }
        }
    }
}
