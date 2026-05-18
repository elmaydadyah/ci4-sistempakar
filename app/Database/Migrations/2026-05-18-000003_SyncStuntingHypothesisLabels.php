<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncStuntingHypothesisLabels extends Migration
{
    public function up()
    {
        $hypotheses = [
            'H1' => [
                'label' => 'Risiko Stunting Tinggi',
                'probabilitas' => 0.20,
                'rekomendasi' => 'Apabila anak terdiagnosis memiliki risiko stunting tinggi, maka diperlukan penanganan segera dengan berkonsultasi ke tenaga kesehatan. Orang tua perlu memperbaiki asupan gizi anak dengan memberikan makanan bergizi seimbang serta melakukan pemantauan pertumbuhan secara rutin agar kondisi tidak semakin memburuk.',
            ],
            'H2' => [
                'label' => 'Risiko Stunting Rendah',
                'probabilitas' => 0.30,
                'rekomendasi' => 'Jika anak berada pada kategori risiko stunting rendah, maka disarankan untuk meningkatkan kualitas pola makan dan menjaga keseimbangan nutrisi. Pemantauan pertumbuhan tetap perlu dilakukan secara berkala untuk mencegah peningkatan risiko.',
            ],
            'H3' => [
                'label' => 'Tidak Memiliki Risiko Stunting',
                'probabilitas' => 0.50,
                'rekomendasi' => 'Apabila anak anda berada dalam kondisi normal (tidak mengalami risiko stunting), maka orang tua perlu mempertahankan pola hidup sehat dengan memberikan asupan gizi seimbang serta melakukan pemantauan pertumbuhan secara rutin ke posyandu agar kondisi tetap optimal.',
            ],
        ];

        if ($this->db->tableExists('tb_naive_bayes_prior')) {
            foreach ($hypotheses as $class => $data) {
                $exists = $this->db->table('tb_naive_bayes_prior')->where('kelas', $class)->countAllResults() > 0;
                if ($exists) {
                    $this->db->table('tb_naive_bayes_prior')->where('kelas', $class)->update($data);
                } else {
                    $this->db->table('tb_naive_bayes_prior')->insert(array_merge(['kelas' => $class], $data));
                }
            }
        }

        if ($this->db->tableExists('tb_hipotesis')) {
            foreach ($hypotheses as $class => $data) {
                $payload = [
                    'risiko_stunting' => $data['label'],
                    'solusi' => $data['rekomendasi'],
                ];

                $exists = $this->db->table('tb_hipotesis')->where('kode_hipotesis', $class)->countAllResults() > 0;
                if ($exists) {
                    $this->db->table('tb_hipotesis')->where('kode_hipotesis', $class)->update($payload);
                } else {
                    $this->db->table('tb_hipotesis')->insert(array_merge(['kode_hipotesis' => $class], $payload));
                }
            }
        }

        if ($this->db->tableExists('tb_hasil_diagnosa')) {
            foreach ($hypotheses as $class => $data) {
                $this->db->table('tb_hasil_diagnosa')
                    ->where('kelas_hasil', $class)
                    ->update([
                        'nama_kasus' => $class . ' - ' . $data['label'],
                        'rekomendasi' => $data['rekomendasi'],
                    ]);
            }
        }

        if ($this->db->tableExists('tb_naive_bayes_likelihood')) {
            foreach ($this->likelihoodMap() as $indicator => $categories) {
                foreach ($categories as $category => $classes) {
                    foreach ($classes as $class => $probability) {
                        $builder = $this->db->table('tb_naive_bayes_likelihood')
                            ->where('indikator', $indicator)
                            ->where('kategori', $category)
                            ->where('kelas', $class);

                        if ($builder->countAllResults(false) > 0) {
                            $builder->update(['probabilitas' => $probability]);
                        } else {
                            $builder->insert([
                                'indikator' => $indicator,
                                'kategori' => $category,
                                'kelas' => $class,
                                'probabilitas' => $probability,
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function down()
    {
        // This migration intentionally keeps the corrected hypothesis meaning.
    }

    private function likelihoodMap(): array
    {
        return [
            'BB/U' => [
                'Berat badan sangat kurang' => ['H1' => 0.73, 'H2' => 0.24, 'H3' => 0.03],
                'Berat badan kurang' => ['H1' => 0.25, 'H2' => 0.65, 'H3' => 0.10],
                'Berat badan normal' => ['H1' => 0.03, 'H2' => 0.13, 'H3' => 0.84],
                'Risiko berat badan lebih' => ['H1' => 0.07, 'H2' => 0.27, 'H3' => 0.66],
            ],
            'TB/U' => [
                'Sangat pendek' => ['H1' => 0.80, 'H2' => 0.18, 'H3' => 0.02],
                'Pendek' => ['H1' => 0.24, 'H2' => 0.68, 'H3' => 0.08],
                'Normal' => ['H1' => 0.03, 'H2' => 0.11, 'H3' => 0.86],
                'Tinggi' => ['H1' => 0.04, 'H2' => 0.14, 'H3' => 0.82],
            ],
            'BB/TB' => [
                'Gizi buruk' => ['H1' => 0.75, 'H2' => 0.22, 'H3' => 0.03],
                'Gizi kurang' => ['H1' => 0.25, 'H2' => 0.66, 'H3' => 0.09],
                'Gizi baik' => ['H1' => 0.03, 'H2' => 0.11, 'H3' => 0.86],
                'Berisiko gizi lebih' => ['H1' => 0.06, 'H2' => 0.22, 'H3' => 0.72],
                'Gizi lebih' => ['H1' => 0.08, 'H2' => 0.27, 'H3' => 0.65],
            ],
        ];
    }
}
