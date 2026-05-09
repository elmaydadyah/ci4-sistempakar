<?php

namespace App\Controllers;

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
                'gejala' => [],
            ],
            'errors' => [],
        ];

        if ($this->request->is('post')) {
            $nama = trim((string) $this->request->getPost('nama'));
            $umur = trim((string) $this->request->getPost('umur'));
            $selectedGejala = array_values(array_filter((array) $this->request->getPost('gejala')));

            $data['old'] = [
                'nama' => $nama,
                'umur' => $umur,
                'gejala' => $selectedGejala,
            ];

            if ($nama === '') {
                $data['errors'][] = 'Nama wajib diisi.';
            }

            if ($umur === '' || !ctype_digit($umur) || (int) $umur < 0 || (int) $umur > 60) {
                $data['errors'][] = 'Umur balita harus diisi dalam rentang 0 sampai 60 bulan.';
            }

            if (count($selectedGejala) === 0) {
                $data['errors'][] = 'Pilih minimal satu gejala untuk memulai analisis.';
            }

            if ($data['errors'] === []) {
                $data['hasil'] = $this->hitungDiagnosa($nama, (int) $umur, $selectedGejala);
            }
        }

        return view('diagnosa/index', $data);
    }

    private function hitungDiagnosa(string $nama, int $umur, array $selectedGejala): array
    {
        $db = db_connect();
        $selectedMap = array_flip(array_map('intval', $selectedGejala));
        $kasus = $db->table('tb_kasus')
            ->orderBy('id_kasus', 'ASC')
            ->get()
            ->getResultArray();
        $relasi = $db->table('tb_kons_detail')
            ->get()
            ->getResultArray();

        $scores = [];
        foreach ($kasus as $item) {
            $scores[(int) $item['id_kasus']] = [
                'kasus' => $item,
                'total' => 0,
                'match' => 0,
                'nilai' => 0,
            ];
        }

        foreach ($relasi as $rule) {
            $idKasus = (int) ($rule['id_kasus'] ?? $rule['id_konsultasi'] ?? 0);
            $idGejala = (int) ($rule['id_gejala'] ?? 0);

            if (!isset($scores[$idKasus])) {
                continue;
            }

            $bobot = isset($rule['nilai']) ? (float) $rule['nilai'] : 1;
            $scores[$idKasus]['total'] += $bobot;

            if (isset($selectedMap[$idGejala])) {
                $scores[$idKasus]['match'] += 1;
                $scores[$idKasus]['nilai'] += $bobot;
            }
        }

        foreach ($scores as &$score) {
            $score['persentase'] = $score['total'] > 0
                ? round(($score['nilai'] / $score['total']) * 100)
                : 0;
        }
        unset($score);

        usort($scores, static fn ($a, $b) => $b['persentase'] <=> $a['persentase']);

        return [
            'nama' => $nama,
            'umur' => $umur,
            'jumlah_gejala' => count($selectedGejala),
            'diagnosa' => $scores[0] ?? null,
        ];
    }
}
