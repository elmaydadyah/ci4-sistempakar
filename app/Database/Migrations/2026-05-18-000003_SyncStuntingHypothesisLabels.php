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
                'rekomendasi' => 'Hasil ini menunjukkan anak perlu diperiksa lebih lanjut. Segera bawa anak ke puskesmas atau dokter spesialis anak untuk penilaian pertumbuhan, status gizi, dan kemungkinan infeksi atau penyakit penyerta. Sambil menunggu pemeriksaan, berikan makanan bergizi seimbang dengan sumber protein setiap hari, pastikan anak cukup cairan, jaga kebersihan makanan dan lingkungan, serta catat berat dan tinggi badan anak secara berkala.',
            ],
            'H2' => [
                'label' => 'Risiko Stunting Sedang',
                'probabilitas' => 0.30,
                'rekomendasi' => 'Anak masih perlu dipantau karena ada beberapa tanda risiko. Lakukan konsultasi ke posyandu, puskesmas, atau dokter spesialis anak bila pertumbuhan tidak naik sesuai kurva, nafsu makan menurun, sering sakit, atau perkembangan anak tampak terlambat. Perbaiki pola makan dengan porsi cukup, lauk berprotein, sayur dan buah, lalu ulangi pengukuran berat dan tinggi badan secara rutin.',
            ],
            'H3' => [
                'label' => 'Risiko Stunting Rendah',
                'probabilitas' => 0.50,
                'rekomendasi' => 'Risiko yang terbaca saat ini rendah, tetapi pemantauan tetap penting. Pertahankan pola makan bergizi seimbang, jadwal makan teratur, imunisasi sesuai usia, kebersihan tangan dan makanan, serta pemeriksaan rutin di posyandu atau fasilitas kesehatan. Jika berat atau tinggi badan tidak bertambah, anak sering sakit, atau muncul kekhawatiran perkembangan, konsultasikan ke dokter spesialis anak.',
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
