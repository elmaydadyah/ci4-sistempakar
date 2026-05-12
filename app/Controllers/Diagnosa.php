<?php

namespace App\Controllers;

use App\Models\HasilDiagnosaModel;
use App\Models\AnakModel;

class Diagnosa extends BaseController
{
    public function index()
    {
        helper(['form']);

        $db = db_connect();
        $gejala = $db->table('tb_gejala')
            ->orderBy('id_gejala', 'ASC')
            ->get()
            ->getResultArray();

        $data = [
            'gejala' => $gejala,
            'hasil' => null,
            'old' => [
                'nama' => '',
                'umur' => '',
                'berat_badan' => '',
                'tinggi_badan' => '',
                'zs_tb_u' => '',
                'gejala' => [],
            ],
            'errors' => [],
        ];

        if (!$this->request->is('post')) {
            $anakId = (int) $this->request->getGet('anak');
            if ($anakId > 0) {
                $anak = (new AnakModel())->find($anakId);

                if ($anak) {
                    $data['old'] = [
                        'nama' => (string) ($anak['nama_anak'] ?? ''),
                        'umur' => (string) ($anak['umur_bulan'] ?? ''),
                        'berat_badan' => $anak['berat_badan'] !== null ? (string) $anak['berat_badan'] : '',
                        'tinggi_badan' => $anak['tinggi_badan'] !== null ? (string) $anak['tinggi_badan'] : '',
                        'zs_tb_u' => '',
                        'gejala' => [],
                    ];
                }
            }
        }

        if ($this->request->is('post')) {
            $nama = trim((string) $this->request->getPost('nama'));
            $umur = trim((string) $this->request->getPost('umur'));
            $beratBadan = trim((string) $this->request->getPost('berat_badan'));
            $tinggiBadan = trim((string) $this->request->getPost('tinggi_badan'));
            $zsTbU = trim((string) $this->request->getPost('zs_tb_u'));
            $selectedGejala = array_values(array_filter((array) $this->request->getPost('gejala')));

            $data['old'] = [
                'nama' => $nama,
                'umur' => $umur,
                'berat_badan' => $beratBadan,
                'tinggi_badan' => $tinggiBadan,
                'zs_tb_u' => $zsTbU,
                'gejala' => $selectedGejala,
            ];

            if ($nama === '') {
                $data['errors'][] = 'Nama wajib diisi.';
            }

            if ($umur === '' || !ctype_digit($umur) || (int) $umur < 0 || (int) $umur > 60) {
                $data['errors'][] = 'Umur balita harus diisi dalam rentang 0 sampai 60 bulan.';
            }

            if ($beratBadan !== '' && !is_numeric($beratBadan)) {
                $data['errors'][] = 'Berat badan harus berupa angka.';
            }

            if ($tinggiBadan !== '' && !is_numeric($tinggiBadan)) {
                $data['errors'][] = 'Tinggi badan harus berupa angka.';
            }

            if ($zsTbU !== '' && !is_numeric($zsTbU)) {
                $data['errors'][] = 'Z-score TB/U harus berupa angka.';
            }

            if (count($selectedGejala) === 0) {
                $data['errors'][] = 'Pilih minimal satu gejala untuk memulai analisis.';
            }

            if ($data['errors'] === []) {
                $data['hasil'] = $this->hitungDiagnosa($nama, (int) $umur, [
                    'berat_badan' => $beratBadan !== '' ? (float) $beratBadan : null,
                    'tinggi_badan' => $tinggiBadan !== '' ? (float) $tinggiBadan : null,
                    'zs_tb_u' => $zsTbU !== '' ? (float) $zsTbU : null,
                ], $selectedGejala);
                $this->simpanHasilDiagnosa($data['hasil']);
            }
        }

        return view('diagnosa/index', $data);
    }

    private function hitungDiagnosa(string $nama, int $umur, array $pengukuran, array $selectedGejala): array
    {
        $db = db_connect();
        $features = [
            'umur_bulan' => $umur,
            'berat' => $pengukuran['berat_badan'],
            'tinggi' => $pengukuran['tinggi_badan'],
            'zs_tb_u' => $pengukuran['zs_tb_u'],
        ];
        $scores = $this->hitungNaiveBayesStatusGizi($db, $features);
        $cfDetail = $this->hitungCertaintyFactor($db, $selectedGejala);
        $diagnosa = $scores[0] ?? null;

        if ($diagnosa !== null) {
            $diagnosa['cf'] = $cfDetail['cf'];
            $diagnosa['persentase'] = (int) round($cfDetail['cf'] * 100);
            $diagnosa['jumlah_gejala_cocok'] = $cfDetail['jumlah_gejala_cocok'];
            $diagnosa['gejala_cf'] = $cfDetail['gejala_cf'];
        }

        return [
            'nama' => $nama,
            'umur' => $umur,
            'pengukuran' => $pengukuran,
            'jumlah_gejala' => count($selectedGejala),
            'diagnosa' => $diagnosa,
            'alternatif' => $scores,
            'jumlah_data_latih' => array_sum(array_column($scores, 'jumlah_data_latih')),
        ];
    }

    private function hitungNaiveBayesStatusGizi($db, array $features): array
    {
        if (!$db->tableExists('tb_anak_status_gizi')) {
            return [];
        }

        $rows = $db->table('tb_anak_status_gizi')
            ->select('usia_saat_ukur, berat, tinggi, zs_tb_u, tb_u')
            ->where('tb_u IS NOT NULL', null, false)
            ->get()
            ->getResultArray();

        $kelas = [
            'Stunting' => [],
            'Tidak Stunting' => [],
        ];

        foreach ($rows as $row) {
            $label = $this->labelStatusGizi($row);
            $umurLatih = $this->parseUsiaBulan($row['usia_saat_ukur'] ?? null);
            $dataLatih = [
                'umur_bulan' => $umurLatih,
                'berat' => isset($row['berat']) ? (float) $row['berat'] : null,
                'tinggi' => isset($row['tinggi']) ? (float) $row['tinggi'] : null,
                'zs_tb_u' => isset($row['zs_tb_u']) ? (float) $row['zs_tb_u'] : null,
            ];

            if (array_filter($dataLatih, static fn ($value) => $value !== null) === []) {
                continue;
            }

            $kelas[$label][] = $dataLatih;
        }

        $totalData = count($kelas['Stunting']) + count($kelas['Tidak Stunting']);
        if ($totalData === 0) {
            return [];
        }

        $scores = [];
        foreach ($kelas as $label => $items) {
            $prior = (count($items) + 1) / ($totalData + count($kelas));
            $score = log($prior);

            foreach ($features as $field => $value) {
                if ($value === null) {
                    continue;
                }

                [$mean, $variance] = $this->meanVariance(array_column($items, $field));
                $score += log($this->gaussianProbability((float) $value, $mean, $variance));
            }

            $scores[] = [
                'kasus' => [
                    'id_kasus' => null,
                    'nama_kasus' => $label,
                    'deskripsi' => $this->getDeskripsiLabel($label),
                    'solusi' => $this->getSolusiLabel($label),
                ],
                'metode' => 'Naive Bayes + Certainty Factor',
                'label_nb' => $label,
                'skor_nb' => $score,
                'jumlah_data_latih' => count($items),
                'probabilitas_nb' => 0.0,
                'probabilitas_nb_persen' => 0,
                'cf' => 0.0,
                'persentase' => 0,
                'jumlah_gejala_cocok' => 0,
            ];
        }

        $maxScore = max(array_column($scores, 'skor_nb'));
        $totalExp = array_sum(array_map(static fn ($item) => exp($item['skor_nb'] - $maxScore), $scores));

        foreach ($scores as &$score) {
            $probability = $totalExp > 0 ? exp($score['skor_nb'] - $maxScore) / $totalExp : 0;
            $score['probabilitas_nb'] = $probability;
            $score['probabilitas_nb_persen'] = (int) round($probability * 100);
        }
        unset($score);

        usort($scores, static fn ($a, $b) => $b['probabilitas_nb'] <=> $a['probabilitas_nb']);

        return $scores;
    }

    private function hitungCertaintyFactor($db, array $selectedGejala): array
    {
        $selectedGejala = array_values(array_filter(array_map('intval', $selectedGejala)));
        if ($selectedGejala === [] || !$db->tableExists('tb_certainty_factor')) {
            return [
                'cf' => 0.0,
                'jumlah_gejala_cocok' => 0,
                'gejala_cf' => [],
            ];
        }

        $rows = $db->table('tb_certainty_factor cf')
            ->select('cf.id_gejala, cf.bobot_cf, g.nama_gejala')
            ->join('tb_gejala g', 'g.id_gejala = cf.id_gejala', 'left')
            ->whereIn('cf.id_gejala', $selectedGejala)
            ->get()
            ->getResultArray();
        $nilaiCf = array_map(fn ($row) => $this->normalisasiBobot($row['bobot_cf'] ?? 0), $rows);

        return [
            'cf' => $this->gabungCertaintyFactor($nilaiCf),
            'jumlah_gejala_cocok' => count($rows),
            'gejala_cf' => $rows,
        ];
    }

    private function labelStatusGizi(array $row): string
    {
        $tbU = strtolower(trim((string) ($row['tb_u'] ?? '')));
        $zsTbU = isset($row['zs_tb_u']) ? (float) $row['zs_tb_u'] : null;

        if (in_array($tbU, ['pendek', 'sangat pendek'], true) || ($zsTbU !== null && $zsTbU < -2)) {
            return 'Stunting';
        }

        return 'Tidak Stunting';
    }

    private function parseUsiaBulan(?string $usia): ?float
    {
        if ($usia === null) {
            return null;
        }

        $usia = strtolower($usia);
        preg_match_all('/\d+(?:[,.]\d+)?/', $usia, $matches);
        $numbers = array_map(static fn ($value) => (float) str_replace(',', '.', $value), $matches[0] ?? []);

        if ($numbers === []) {
            return null;
        }

        if (str_contains($usia, 'tahun')) {
            $bulan = $numbers[0] * 12;

            if (isset($numbers[1]) && str_contains($usia, 'bulan')) {
                $bulan += $numbers[1];
            }

            return $bulan;
        }

        return $numbers[0];
    }

    private function meanVariance(array $values): array
    {
        $values = array_values(array_filter($values, static fn ($value) => $value !== null && is_numeric($value)));

        if ($values === []) {
            return [0.0, 1.0];
        }

        $mean = array_sum($values) / count($values);
        $variance = 0.0;

        foreach ($values as $value) {
            $variance += ((float) $value - $mean) ** 2;
        }

        $variance = $variance / max(count($values), 1);

        return [$mean, max($variance, 0.0001)];
    }

    private function gaussianProbability(float $value, float $mean, float $variance): float
    {
        $exponent = exp(-(($value - $mean) ** 2) / (2 * $variance));

        return max((1 / sqrt(2 * M_PI * $variance)) * $exponent, 0.0000001);
    }

    private function getDeskripsiLabel(string $label): string
    {
        if ($label === 'Stunting') {
            return 'Data pengukuran anak lebih dekat dengan pola status gizi anak stunting pada data status gizi.';
        }

        return 'Data pengukuran anak lebih dekat dengan pola status gizi anak tidak stunting pada data status gizi.';
    }

    private function getSolusiLabel(string $label): string
    {
        if ($label === 'Stunting') {
            return 'Lakukan pemantauan pertumbuhan, perbaikan asupan gizi, dan konsultasi lanjutan dengan petugas kesehatan.';
        }

        return 'Pertahankan pemantauan tumbuh kembang, pola makan bergizi, dan pemeriksaan rutin di posyandu atau fasilitas kesehatan.';
    }

    private function normalisasiBobot($nilai): float
    {
        $bobot = is_numeric($nilai) ? (float) $nilai : 1.0;

        if ($bobot > 1) {
            $bobot /= 100;
        }

        return max(0.0, min(1.0, $bobot));
    }

    private function gabungCertaintyFactor(array $nilaiCf): float
    {
        $combined = 0.0;

        foreach ($nilaiCf as $cf) {
            $cf = max(0.0, min(1.0, (float) $cf));
            $combined = $combined + ($cf * (1 - $combined));
        }

        return $combined;
    }

    private function simpanHasilDiagnosa(array $hasil): void
    {
        $db = db_connect();
        if (!$db->tableExists('tb_hasil_diagnosa')) {
            return;
        }

        $diagnosa = $hasil['diagnosa'] ?? [];
        $kasus = $diagnosa['kasus'] ?? [];

        $model = new HasilDiagnosaModel();
        $model->insert([
            'nama' => $hasil['nama'] ?? '-',
            'umur' => (int) ($hasil['umur'] ?? 0),
            'id_kasus' => isset($kasus['id_kasus']) ? (int) $kasus['id_kasus'] : null,
            'nama_kasus' => $kasus['nama_kasus'] ?? null,
            'persentase' => (int) ($diagnosa['persentase'] ?? 0),
            'jumlah_gejala' => (int) ($hasil['jumlah_gejala'] ?? 0),
        ]);
    }
}
