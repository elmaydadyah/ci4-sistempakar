<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTbHipotesis extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_hipotesis')) {
            $this->forge->addField([
                'id_hipotesis' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'auto_increment' => true,
                ],
                'kode_hipotesis' => [
                    'type' => 'VARCHAR',
                    'constraint' => 10,
                ],
                'risiko_stunting' => [
                    'type' => 'VARCHAR',
                    'constraint' => 150,
                ],
                'solusi' => [
                    'type' => 'TEXT',
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

            $this->forge->addKey('id_hipotesis', true);
            $this->forge->addUniqueKey('kode_hipotesis');
            $this->forge->createTable('tb_hipotesis', true);
        }

        $this->seedHipotesis();
    }

    public function down()
    {
        $this->forge->dropTable('tb_hipotesis', true);
    }

    private function seedHipotesis(): void
    {
        if (!$this->db->tableExists('tb_hipotesis')) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $rows = [
            [
                'kode_hipotesis' => 'H1',
                'risiko_stunting' => 'Risiko Stunting Tinggi',
                'solusi' => 'Hasil ini menunjukkan anak perlu diperiksa lebih lanjut. Segera bawa anak ke puskesmas atau dokter spesialis anak untuk penilaian pertumbuhan, status gizi, dan kemungkinan infeksi atau penyakit penyerta. Sambil menunggu pemeriksaan, berikan makanan bergizi seimbang dengan sumber protein setiap hari, pastikan anak cukup cairan, jaga kebersihan makanan dan lingkungan, serta catat berat dan tinggi badan anak secara berkala.',
            ],
            [
                'kode_hipotesis' => 'H2',
                'risiko_stunting' => 'Risiko Stunting Sedang',
                'solusi' => 'Anak masih perlu dipantau karena ada beberapa tanda risiko. Lakukan konsultasi ke posyandu, puskesmas, atau dokter spesialis anak bila pertumbuhan tidak naik sesuai kurva, nafsu makan menurun, sering sakit, atau perkembangan anak tampak terlambat. Perbaiki pola makan dengan porsi cukup, lauk berprotein, sayur dan buah, lalu ulangi pengukuran berat dan tinggi badan secara rutin.',
            ],
            [
                'kode_hipotesis' => 'H3',
                'risiko_stunting' => 'Risiko Stunting Rendah',
                'solusi' => 'Risiko yang terbaca saat ini rendah, tetapi pemantauan tetap penting. Pertahankan pola makan bergizi seimbang, jadwal makan teratur, imunisasi sesuai usia, kebersihan tangan dan makanan, serta pemeriksaan rutin di posyandu atau fasilitas kesehatan. Jika berat atau tinggi badan tidak bertambah, anak sering sakit, atau muncul kekhawatiran perkembangan, konsultasikan ke dokter spesialis anak.',
            ],
        ];

        foreach ($rows as $row) {
            $existing = $this->db->table('tb_hipotesis')
                ->select('id_hipotesis')
                ->where('kode_hipotesis', $row['kode_hipotesis'])
                ->get()
                ->getRowArray();

            $payload = $row + [
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($existing) {
                unset($payload['created_at']);
                $this->db->table('tb_hipotesis')
                    ->where('kode_hipotesis', $row['kode_hipotesis'])
                    ->update($payload);
                continue;
            }

            $this->db->table('tb_hipotesis')->insert($payload);
        }
    }
}
