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
                'solusi' => 'Apabila anak terdiagnosis memiliki risiko stunting tinggi, maka diperlukan penanganan segera dengan berkonsultasi ke tenaga kesehatan. Orang tua perlu memperbaiki asupan gizi anak dengan memberikan makanan bergizi seimbang serta melakukan pemantauan pertumbuhan secara rutin agar kondisi tidak semakin memburuk.',
            ],
            [
                'kode_hipotesis' => 'H2',
                'risiko_stunting' => 'Risiko Stunting Rendah',
                'solusi' => 'Jika anak berada pada kategori risiko stunting rendah, maka disarankan untuk meningkatkan kualitas pola makan dan menjaga keseimbangan nutrisi. Pemantauan pertumbuhan tetap perlu dilakukan secara berkala untuk mencegah peningkatan risiko.',
            ],
            [
                'kode_hipotesis' => 'H3',
                'risiko_stunting' => 'Tidak Memiliki Risiko Stunting',
                'solusi' => 'Apabila anak anda berada dalam kondisi normal (tidak mengalami risiko stunting), maka orang tua perlu mempertahankan pola hidup sehat dengan memberikan asupan gizi seimbang serta melakukan pemantauan pertumbuhan secara rutin ke posyandu agar kondisi tetap optimal.',
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
