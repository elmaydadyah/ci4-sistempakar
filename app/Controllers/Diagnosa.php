<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\HasilDiagnosaModel;

class Diagnosa extends BaseController
{
    private array $kelas = [
        'H1' => 'Risiko Stunting Tinggi',
        'H2' => 'Risiko Stunting Rendah',
        'H3' => 'Tidak Memiliki Risiko Stunting',
    ];

    public function index()
    {
        helper(['form']);

        $data = [
            'hasil' => null,
            'old' => $this->emptyOldInput(),
            'errors' => [],
            'tb_gejala' => $this->getGejalaPertanyaan(),
        ];

        if (!$this->request->is('post')) {
            $data['old'] = $this->oldInputFromAnak((int) $this->request->getGet('anak'));
            $data['hasil'] = $this->getHasilDiagnosaFromRequest();

            if ($data['hasil'] !== null) {
                $data['old'] = array_merge($data['old'], $this->oldInputFromStoredHasil($data['hasil']));
            }
        }

        if ($this->request->is('post')) {
            $input = $this->getPostInput();
            $data['old'] = $input;
            $data['errors'] = $this->validateInput($input);

            if ($data['errors'] === []) {
                $data['hasil'] = $this->hitungDiagnosa($input);
                $this->simpanAnak($data['hasil']);
                $this->simpanHasilDiagnosa($data['hasil']);

                if (!empty($data['hasil']['id_hasil_diagnosa'])) {
                    session()->set('last_hasil_diagnosa_id', (int) $data['hasil']['id_hasil_diagnosa']);
                    return redirect()->to('/konsultasi?hasil=' . $data['hasil']['id_hasil_diagnosa']);
                }
            }
        }

        return view('diagnosa/index', $data);
    }

    public function laporan(int $id)
    {
        if ($id <= 0 || !db_connect()->tableExists('tb_hasil_diagnosa')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan tidak ditemukan.');
        }

        $row = (new HasilDiagnosaModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan tidak ditemukan.');
        }

        $hasil = $this->formatStoredHasilDiagnosa($row);

        return view('diagnosa/laporan', [
            'hasil' => $hasil,
            'row' => $row,
            'tanggal_cetak' => date('d/m/Y H:i'),
        ]);
    }

    private function getHasilDiagnosaFromRequest(): ?array
    {
        $hasilId = (int) ($this->request->getGet('hasil') ?? 0);
        if ($hasilId <= 0) {
            $hasilId = (int) (session()->get('last_hasil_diagnosa_id') ?? 0);
        }

        if ($hasilId <= 0 || !db_connect()->tableExists('tb_hasil_diagnosa')) {
            return null;
        }

        $row = (new HasilDiagnosaModel())->find($hasilId);
        if (!$row) {
            return null;
        }

        return $this->formatStoredHasilDiagnosa($row);
    }

    private function formatStoredHasilDiagnosa(array $row): array
    {
        $alternatif = json_decode((string) ($row['probabilitas_posterior'] ?? '[]'), true);
        $alternatif = is_array($alternatif) ? $alternatif : [];
        $alternatif = array_map(function ($item) {
            if (!is_array($item)) {
                return $item;
            }

            $class = (string) ($item['kelas'] ?? '');
            if ($class !== '' && isset($this->kelas[$class])) {
                $item['label'] = $this->kelas[$class];
            }

            return $item;
        }, $alternatif);
        $diagnosa = $alternatif[0] ?? [
            'kelas' => $row['kelas_hasil'] ?? 'H1',
            'label' => $this->kelas[$row['kelas_hasil'] ?? 'H1'] ?? 'Risiko Stunting Tinggi',
            'posterior_persen' => (int) ($row['persentase'] ?? 0),
            'posterior' => ((int) ($row['persentase'] ?? 0)) / 100,
        ];
        if (!empty($diagnosa['kelas']) && isset($this->kelas[$diagnosa['kelas']])) {
            $diagnosa['label'] = $this->kelas[$diagnosa['kelas']];
        }

        $gejala = json_decode((string) ($row['gejala_zscore'] ?? '[]'), true);
        $gejala = is_array($gejala) ? $gejala : [];
        $prior = json_decode((string) ($row['probabilitas_prior'] ?? '[]'), true);
        $likelihood = json_decode((string) ($row['probabilitas_likelihood'] ?? '[]'), true);

        return [
            'id_hasil_diagnosa' => (int) ($row['id_hasil_diagnosa'] ?? 0),
            'id_anak' => $row['id_anak'] ?? null,
            'input' => [],
            'nama' => (string) ($row['nama'] ?? '-'),
            'umur' => (int) ($row['umur'] ?? 0),
            'pengukuran' => [
                'berat_badan' => $row['berat_badan'] ?? null,
                'tinggi_badan' => $row['tinggi_badan'] ?? null,
            ],
            'zscore' => [
                'bb_u' => [
                    'label' => 'BB/U',
                    'nilai' => $row['zs_bb_u'] ?? null,
                    'kategori' => (string) ($row['kategori_bb_u'] ?? '-'),
                    'sumber' => 'Data hasil tersimpan',
                ],
                'tb_u' => [
                    'label' => 'TB/U',
                    'nilai' => $row['zs_tb_u'] ?? null,
                    'kategori' => (string) ($row['kategori_tb_u'] ?? '-'),
                    'sumber' => 'Data hasil tersimpan',
                ],
                'bb_tb' => [
                    'label' => 'BB/TB',
                    'nilai' => $row['zs_bb_tb'] ?? null,
                    'kategori' => (string) ($row['kategori_bb_tb'] ?? '-'),
                    'sumber' => 'Data hasil tersimpan',
                ],
            ],
            'gejala' => $gejala,
            'gejala_tambahan' => [],
            'gejala_terbaca' => $gejala,
            'jumlah_gejala' => (int) ($row['jumlah_gejala'] ?? count($gejala)),
            'diagnosa' => $diagnosa,
            'alternatif' => $alternatif,
            'prior' => is_array($prior) ? $prior : [],
            'likelihood' => is_array($likelihood) ? $likelihood : [],
            'jumlah_data_latih' => 0,
            'persentase' => (int) ($row['persentase'] ?? ($diagnosa['posterior_persen'] ?? 0)),
            'rekomendasi' => $this->getRekomendasi((string) ($diagnosa['kelas'] ?? 'H1')),
            'stored_row' => $row,
        ];
    }

    private function oldInputFromStoredHasil(array $hasil): array
    {
        $row = $hasil['stored_row'] ?? [];

        return [
            'nama' => (string) ($row['nama'] ?? ''),
            'nik' => (string) ($row['nik'] ?? ''),
            'jenis_kelamin' => (string) ($row['jenis_kelamin'] ?? ''),
            'tanggal_lahir' => (string) ($row['tanggal_lahir'] ?? ''),
            'umur' => (string) ($row['umur'] ?? ''),
            'berat_badan' => $row['berat_badan'] !== null ? (string) $row['berat_badan'] : '',
            'tinggi_badan' => $row['tinggi_badan'] !== null ? (string) $row['tinggi_badan'] : '',
            'lingkar_lengan' => $row['lingkar_lengan'] !== null ? (string) $row['lingkar_lengan'] : '',
            'lingkar_kepala' => $row['lingkar_kepala'] !== null ? (string) $row['lingkar_kepala'] : '',
            'nama_ortu' => '',
            'alamat' => '',
            'tempat_tinggal' => (string) ($row['tempat_tinggal'] ?? ''),
            'riwayat_kehamilan' => (string) ($row['riwayat_kehamilan'] ?? ''),
            'pola_makan' => (string) ($row['pola_makan'] ?? ''),
            'jawaban_gejala' => [],
        ];
    }

    private function emptyOldInput(): array
    {
        return [
            'nama' => '',
            'nik' => '',
            'jenis_kelamin' => '',
            'tanggal_lahir' => '',
            'umur' => '',
            'berat_badan' => '',
            'tinggi_badan' => '',
            'lingkar_lengan' => '',
            'lingkar_kepala' => '',
            'nama_ortu' => '',
            'alamat' => '',
            'tempat_tinggal' => '',
            'riwayat_kehamilan' => '',
            'pola_makan' => '',
            'jawaban_gejala' => [],
        ];
    }

    private function oldInputFromAnak(int $anakId): array
    {
        $old = $this->emptyOldInput();

        if ($anakId <= 0) {
            return $old;
        }

        $anak = (new AnakModel())->find($anakId);
        if (!$anak) {
            return $old;
        }

        return array_merge($old, [
            'nama' => (string) ($anak['nama_anak'] ?? ''),
            'nik' => (string) ($anak['nik'] ?? ''),
            'jenis_kelamin' => (string) ($anak['jenis_kelamin'] ?? ''),
            'tanggal_lahir' => (string) ($anak['tanggal_lahir'] ?? ''),
            'umur' => (string) ($anak['umur_bulan'] ?? ''),
            'berat_badan' => $anak['berat_badan'] !== null ? (string) $anak['berat_badan'] : '',
            'tinggi_badan' => $anak['tinggi_badan'] !== null ? (string) $anak['tinggi_badan'] : '',
            'lingkar_lengan' => $anak['lingkar_lengan'] !== null ? (string) $anak['lingkar_lengan'] : '',
            'lingkar_kepala' => $anak['lingkar_kepala'] !== null ? (string) $anak['lingkar_kepala'] : '',
            'nama_ortu' => (string) ($anak['nama_ortu'] ?? ''),
            'alamat' => (string) ($anak['alamat'] ?? ''),
            'tempat_tinggal' => (string) ($anak['tempat_tinggal'] ?? ''),
            'riwayat_kehamilan' => (string) ($anak['riwayat_kehamilan'] ?? ''),
            'pola_makan' => (string) ($anak['pola_makan'] ?? ''),
            'jawaban_gejala' => [],
        ]);
    }

    private function getPostInput(): array
    {
        $jawabanGejala = $this->request->getPost('jawaban_gejala');
        $jawabanGejala = is_array($jawabanGejala) ? $jawabanGejala : [];

        return [
            'nama' => trim((string) $this->request->getPost('nama')),
            'nik' => trim((string) $this->request->getPost('nik')),
            'jenis_kelamin' => trim((string) $this->request->getPost('jenis_kelamin')),
            'tanggal_lahir' => trim((string) $this->request->getPost('tanggal_lahir')),
            'umur' => trim((string) $this->request->getPost('umur')),
            'berat_badan' => trim((string) $this->request->getPost('berat_badan')),
            'tinggi_badan' => trim((string) $this->request->getPost('tinggi_badan')),
            'lingkar_lengan' => trim((string) $this->request->getPost('lingkar_lengan')),
            'lingkar_kepala' => trim((string) $this->request->getPost('lingkar_kepala')),
            'nama_ortu' => trim((string) $this->request->getPost('nama_ortu')),
            'alamat' => trim((string) $this->request->getPost('alamat')),
            'tempat_tinggal' => trim((string) $this->request->getPost('tempat_tinggal')),
            'riwayat_kehamilan' => trim((string) $this->request->getPost('riwayat_kehamilan')),
            'pola_makan' => trim((string) $this->request->getPost('pola_makan')),
            'jawaban_gejala' => $jawabanGejala,
        ];
    }

    private function validateInput(array $input): array
    {
        $errors = [];

        if ($input['nama'] === '') {
            $errors[] = 'Nama balita wajib diisi.';
        }

        if ($input['nik'] !== '' && !preg_match('/^[0-9]{8,32}$/', $input['nik'])) {
            $errors[] = 'NIK diisi angka minimal 8 digit.';
        }

        if (!in_array($input['jenis_kelamin'], ['L', 'P'], true)) {
            $errors[] = 'Jenis kelamin wajib dipilih.';
        }

        if ($input['umur'] === '' || !ctype_digit($input['umur']) || (int) $input['umur'] < 0 || (int) $input['umur'] > 60) {
            $errors[] = 'Umur balita harus 0 sampai 60 bulan.';
        }

        foreach (['berat_badan' => 'Berat badan', 'tinggi_badan' => 'Tinggi badan'] as $field => $label) {
            if ($input[$field] === '' || !is_numeric($input[$field]) || (float) $input[$field] <= 0) {
                $errors[] = $label . ' wajib diisi dengan angka lebih dari 0.';
            }
        }

        foreach (['lingkar_lengan' => 'Lingkar lengan', 'lingkar_kepala' => 'Lingkar kepala'] as $field => $label) {
            if ($input[$field] !== '' && (!is_numeric($input[$field]) || (float) $input[$field] <= 0)) {
                $errors[] = $label . ' harus berupa angka lebih dari 0.';
            }
        }

        return $errors;
    }

    private function hitungDiagnosa(array $input): array
    {
        $db = db_connect();
        $pengukuran = [
            'umur_bulan' => (int) $input['umur'],
            'jenis_kelamin' => $input['jenis_kelamin'],
            'berat_badan' => (float) $input['berat_badan'],
            'tinggi_badan' => (float) $input['tinggi_badan'],
            'lingkar_lengan' => $input['lingkar_lengan'] !== '' ? (float) $input['lingkar_lengan'] : null,
            'lingkar_kepala' => $input['lingkar_kepala'] !== '' ? (float) $input['lingkar_kepala'] : null,
        ];

        $zscore = $this->hitungZScore($db, $pengukuran);
        $gejala = $this->konversiZScoreMenjadiGejala($db, $zscore);
        $gejalaTambahan = $this->gejalaTambahanDariJawaban($db, $input['jawaban_gejala'] ?? []);
        $gejalaTerbaca = array_merge($gejala, $gejalaTambahan);
        $bayes = $this->hitungNaiveBayes($db, $gejalaTerbaca);
        $diagnosa = $bayes['hasil'];
        $posterior = $diagnosa['posterior'] ?? 0.0;

        return [
            'input' => $input,
            'nama' => $input['nama'],
            'umur' => (int) $input['umur'],
            'pengukuran' => $pengukuran,
            'zscore' => $zscore,
            'gejala' => $gejala,
            'gejala_tambahan' => $gejalaTambahan,
            'gejala_terbaca' => $gejalaTerbaca,
            'jumlah_gejala' => count($gejalaTerbaca),
            'diagnosa' => $diagnosa,
            'alternatif' => $bayes['alternatif'],
            'prior' => $bayes['prior'],
            'likelihood' => $bayes['likelihood'],
            'jumlah_data_latih' => $bayes['jumlah_data_latih'],
            'persentase' => (int) round($posterior * 100),
            'rekomendasi' => $this->getRekomendasi($diagnosa['kelas'] ?? 'H1'),
        ];
    }

    private function getGejalaPertanyaan(): array
    {
        $db = db_connect();
        if (!$db->tableExists('tb_gejala')) {
            return [];
        }

        $rows = $db->table('tb_gejala')
            ->select('id_gejala, kode_gejala, nama_gejala')
            ->whereNotIn('kode_gejala', ['G01', 'G02', 'G11'])
            ->orderBy('id_gejala', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(function ($row) {
            $row['pertanyaan_gejala'] = $this->pertanyaanGejala(
                (string) ($row['kode_gejala'] ?? ''),
                (string) ($row['nama_gejala'] ?? '')
            );

            return $row;
        }, $rows);
    }

    private function pertanyaanGejala(string $kodeGejala, string $namaGejala): string
    {
        $pertanyaan = [
            'G01' => 'Apakah berat badan anak cenderung kurang atau tidak sesuai dengan anak seusianya?',
            'G02' => 'Apakah tinggi badan anak lebih pendek atau lebih rendah dari standar anak seusianya?',
            'G03' => 'Apakah anak terlihat kurang aktif dalam aktivitas sehari-hari?',
            'G04' => 'Apakah anak sering sakit atau daya tahan tubuhnya terlihat rendah?',
            'G05' => 'Apakah anak mengalami keterlambatan dalam berbicara?',
            'G06' => 'Apakah perkembangan keterampilan fisik anak lambat, seperti berguling, duduk, berdiri, atau berjalan?',
            'G07' => 'Apakah anak susah fokus saat diajak bermain, belajar, atau berinteraksi?',
            'G08' => 'Apakah gigi susu atau gigi permanen anak terlambat tumbuh?',
            'G09' => 'Apakah ada riwayat keluarga yang mengalami stunting?',
            'G10' => 'Apakah nafsu makan anak berkurang?',
            'G11' => 'Apakah anak terlihat mengalami kekurangan gizi?',
            'G12' => 'Apakah kepala anak terlihat lebih besar dibandingkan badannya?',
            'G13' => 'Apakah kulit anak terlihat kering dan rambutnya tampak tipis?',
            'G14' => 'Apakah anak sering mengalami mimisan tanpa sebab yang jelas?',
            'G15' => 'Apakah ibu mengalami anemia saat hamil?',
            'G16' => 'Apakah ibu sering merasa lemas, pusing, dan mudah lelah saat hamil?',
            'G17' => 'Apakah ibu mengalami penurunan atau kenaikan berat badan yang tidak normal saat hamil?',
            'G18' => 'Apakah ibu mengalami Kekurangan Energi Kronis (KEK) selama masa kehamilan?',
            'G19' => 'Apakah keluarga menerapkan gaya hidup bersih dan sehat di lingkungan rumah?',
            'G20' => 'Apakah anak tidak memiliki respon yang baik saat diajak berkomunikasi?',
        ];

        if (isset($pertanyaan[$kodeGejala])) {
            return $pertanyaan[$kodeGejala];
        }

        return 'Apakah anak mengalami ' . strtolower($namaGejala) . '?';
    }

    private function gejalaTambahanDariJawaban($db, array $jawabanGejala): array
    {
        $jawabanYa = array_keys(array_filter($jawabanGejala, static fn ($jawaban) => $jawaban === 'ya'));
        $ids = array_values(array_filter(array_map('intval', $jawabanYa), static fn ($id) => $id > 0));

        if ($ids === [] || !$db->tableExists('tb_gejala')) {
            return [];
        }

        $rows = $db->table('tb_gejala')
            ->select('id_gejala, kode_gejala, nama_gejala')
            ->whereIn('id_gejala', $ids)
            ->orderBy('id_gejala', 'ASC')
            ->get()
            ->getResultArray();

        return array_map(static fn ($row) => [
            'kode' => (string) ($row['kode_gejala'] ?? ('G' . $row['id_gejala'])),
            'indikator' => 'Pertanyaan gejala',
            'nama' => (string) ($row['nama_gejala'] ?? ''),
            'kategori' => 'Ya',
            'id_gejala' => (int) ($row['id_gejala'] ?? 0),
        ], $rows);
    }

    private function hitungZScore($db, array $pengukuran): array
    {
        $bbU = $this->zScoreFromReference($db, 'berat', $pengukuran['berat_badan'], $pengukuran);
        $tbU = $this->zScoreFromReference($db, 'tinggi', $pengukuran['tinggi_badan'], $pengukuran);
        $bbTb = $this->zScoreBbTb($db, $pengukuran);

        return [
            'bb_u' => [
                'label' => 'BB/U',
                'nilai' => $bbU,
                'kategori' => $this->kategoriBbU($bbU),
                'sumber' => $bbU === null ? 'Belum tersedia' : 'Referensi data status gizi dan ambang WHO',
            ],
            'tb_u' => [
                'label' => 'TB/U',
                'nilai' => $tbU,
                'kategori' => $this->kategoriTbU($tbU),
                'sumber' => $tbU === null ? 'Belum tersedia' : 'Referensi data status gizi dan ambang WHO',
            ],
            'bb_tb' => [
                'label' => 'BB/TB',
                'nilai' => $bbTb,
                'kategori' => $this->kategoriBbTb($bbTb),
                'sumber' => $bbTb === null ? 'Belum tersedia' : 'Referensi data status gizi dan ambang WHO',
            ],
        ];
    }

    private function zScoreFromReference($db, string $field, float $value, array $pengukuran): ?float
    {
        $indicator = $field === 'berat' ? 'BB/U' : 'TB/U';
        $row = $this->getEditableStandardRow($db, $indicator, $pengukuran);
        if ($row !== null) {
            return $this->zScoreFromStandardRow($value, $row);
        }

        [$mean, $sd] = $this->fallbackReference($indicator, $pengukuran);

        if ($mean === null || $sd === null || $sd <= 0) {
            return null;
        }

        return round(($value - $mean) / $sd, 2);
    }

    private function zScoreBbTb($db, array $pengukuran): ?float
    {
        $row = $this->getEditableStandardRow($db, 'BB/TB', $pengukuran);
        if ($row !== null) {
            return $this->zScoreFromStandardRow((float) $pengukuran['berat_badan'], $row);
        }

        [$mean, $sd] = $this->fallbackReference('BB/TB', $pengukuran);

        if ($mean === null || $sd === null || $sd <= 0) {
            return null;
        }

        return round(($pengukuran['berat_badan'] - $mean) / $sd, 2);
    }

    private function getEditableStandardRow($db, string $indicator, array $pengukuran): ?array
    {
        if (!$db->tableExists('tb_standar_antropometri')) {
            return null;
        }

        $builder = $db->table('tb_standar_antropometri')
            ->select('median, sd, sd_neg3, sd_neg2, sd_neg1, sd_pos1, sd_pos2, sd_pos3, umur_bulan, umur_min_bulan, umur_max_bulan, tinggi_cm')
            ->where('indikator', $indicator)
            ->where('jenis_kelamin', $pengukuran['jenis_kelamin']);

        if ($indicator === 'BB/TB') {
            $targetHeight = (float) $pengukuran['tinggi_badan'];
            $targetAge = (int) $pengukuran['umur_bulan'];
            $rows = $builder
                ->where('tinggi_cm IS NOT NULL', null, false)
                ->groupStart()
                    ->where('umur_min_bulan IS NULL', null, false)
                    ->orWhere('umur_min_bulan <=', $targetAge)
                ->groupEnd()
                ->groupStart()
                    ->where('umur_max_bulan IS NULL', null, false)
                    ->orWhere('umur_max_bulan >=', $targetAge)
                ->groupEnd()
                ->orderBy('CASE WHEN umur_min_bulan IS NULL THEN 1 ELSE 0 END', '', false)
                ->orderBy("ABS(tinggi_cm - {$targetHeight})", '', false)
                ->limit(1)
                ->get()
                ->getResultArray();
        } else {
            $targetAge = (int) $pengukuran['umur_bulan'];
            $rows = $builder
                ->where('umur_bulan IS NOT NULL', null, false)
                ->orderBy("ABS(umur_bulan - {$targetAge})", '', false)
                ->limit(1)
                ->get()
                ->getResultArray();
        }

        $row = $rows[0] ?? null;
        if (!$row || !is_numeric($row['median'] ?? null) || !is_numeric($row['sd'] ?? null)) {
            return null;
        }

        return $row;
    }

    private function zScoreFromStandardRow(float $value, array $row): ?float
    {
        $points = [
            -3 => $row['sd_neg3'] ?? null,
            -2 => $row['sd_neg2'] ?? null,
            -1 => $row['sd_neg1'] ?? null,
            0 => $row['median'] ?? null,
            1 => $row['sd_pos1'] ?? null,
            2 => $row['sd_pos2'] ?? null,
            3 => $row['sd_pos3'] ?? null,
        ];

        $hasCompleteSdTable = true;
        foreach ($points as $pointValue) {
            if ($pointValue === null || $pointValue === '' || !is_numeric($pointValue)) {
                $hasCompleteSdTable = false;
                break;
            }
        }

        if ($hasCompleteSdTable) {
            $previousScore = null;
            $previousValue = null;

            foreach ($points as $score => $standardValue) {
                $standardValue = (float) $standardValue;

                if ($value === $standardValue) {
                    return (float) $score;
                }

                if ($previousValue !== null && $value < $standardValue) {
                    $range = $standardValue - $previousValue;
                    if ($range <= 0) {
                        return (float) $score;
                    }

                    return round($previousScore + (($value - $previousValue) / $range), 2);
                }

                $previousScore = (float) $score;
                $previousValue = $standardValue;
            }

            $lastStep = (float) $points[3] - (float) $points[2];
            if ($value < (float) $points[-3]) {
                $firstStep = (float) $points[-2] - (float) $points[-3];
                return $firstStep > 0 ? round(-3 + (($value - (float) $points[-3]) / $firstStep), 2) : -3.0;
            }

            return $lastStep > 0 ? round(3 + (($value - (float) $points[3]) / $lastStep), 2) : 3.0;
        }

        $median = (float) ($row['median'] ?? 0);
        $sd = (float) ($row['sd'] ?? 0);

        if ($sd <= 0) {
            return null;
        }

        return round(($value - $median) / $sd, 2);
    }

    private function getReferenceRows($db, string $field, array $pengukuran, bool $byHeight = false): array
    {
        if (!$db->tableExists('tb_anak_status_gizi') || !$db->fieldExists($field, 'tb_anak_status_gizi')) {
            return [];
        }

        $builder = $db->table('tb_anak_status_gizi')
            ->select('jk, usia_saat_ukur, berat, tinggi')
            ->where($field . ' IS NOT NULL', null, false);

        $gender = $pengukuran['jenis_kelamin'] === 'L' ? ['L', 'LK', 'LAKI-LAKI', 'Laki-laki'] : ['P', 'PR', 'PEREMPUAN', 'Perempuan'];
        if ($db->fieldExists('jk', 'tb_anak_status_gizi')) {
            $builder->groupStart();
            foreach ($gender as $item) {
                $builder->orWhere('jk', $item);
            }
            $builder->groupEnd();
        }

        $rows = $builder->get()->getResultArray();
        $age = (int) $pengukuran['umur_bulan'];
        $height = (float) $pengukuran['tinggi_badan'];

        $filtered = array_values(array_filter($rows, function ($row) use ($age, $height, $byHeight) {
            if ($byHeight) {
                return isset($row['tinggi']) && abs((float) $row['tinggi'] - $height) <= 4;
            }

            $rowAge = $this->parseUsiaBulan($row['usia_saat_ukur'] ?? null);

            return $rowAge !== null && abs($rowAge - $age) <= 2;
        }));

        if (count($filtered) >= 5) {
            return $filtered;
        }

        return array_slice($rows, 0, 500);
    }

    private function fallbackReference(string $field, array $pengukuran): array
    {
        $age = (int) $pengukuran['umur_bulan'];
        $height = (float) $pengukuran['tinggi_badan'];
        $genderOffset = $pengukuran['jenis_kelamin'] === 'L' ? 0.2 : 0.0;

        if ($field === 'tinggi' || $field === 'TB/U') {
            $median = $age <= 24 ? 49.5 + ($age * 1.55) : 86.5 + (($age - 24) * 0.72);
            return [$median + $genderOffset, 3.1];
        }

        if ($field === 'berat' || $field === 'BB/U') {
            $median = $age <= 12 ? 3.3 + ($age * 0.48) : 9.0 + (($age - 12) * 0.18);
            return [$median + $genderOffset, 1.25];
        }

        if ($field === 'bb_tb' || $field === 'BB/TB') {
            $median = max(4.0, 2.2 + ($height * 0.105));
            return [$median + $genderOffset, 1.15];
        }

        return [null, null];
    }

    private function konversiZScoreMenjadiGejala($db, array $zscore): array
    {
        $ruleBasedGejala = $this->konversiZScoreDenganRuleBased($db, $zscore);
        if ($ruleBasedGejala !== null) {
            return $ruleBasedGejala;
        }

        $gejala = [];
        $kodeGejalaByIndikator = [
            'bb_u' => [
                'kode' => 'G01',
                'nama' => 'Berat badan kurang',
                'kategori_gejala' => ['Berat badan sangat kurang', 'Berat badan kurang'],
            ],
            'tb_u' => [
                'kode' => 'G02',
                'nama' => 'Tinggi badan kurang',
                'kategori_gejala' => ['Sangat pendek', 'Pendek'],
            ],
            'bb_tb' => [
                'kode' => 'G11',
                'nama' => 'Kekurangan gizi',
                'kategori_gejala' => ['Gizi buruk', 'Gizi kurang'],
            ],
        ];
        $masterGejala = $this->getMasterGejalaByKode($db, array_column($kodeGejalaByIndikator, 'kode'));

        foreach ($zscore as $key => $item) {
            if (($item['nilai'] ?? null) === null) {
                continue;
            }

            $kategori = (string) ($item['kategori'] ?? '');
            $mapping = $kodeGejalaByIndikator[$key] ?? null;
            if ($mapping !== null && !in_array($kategori, $mapping['kategori_gejala'], true)) {
                continue;
            }

            $kode = $mapping['kode'] ?? $key . ':' . strtolower(str_replace(' ', '_', $kategori));
            $master = $masterGejala[$kode] ?? [];

            $gejala[] = [
                'kode' => $kode,
                'indikator' => $item['label'],
                'nama' => ($master['nama_gejala'] ?? $mapping['nama'] ?? $item['label']) . ' (' . $item['label'] . ' - ' . $kategori . ')',
                'kategori' => $kategori,
                'zscore' => $item['nilai'],
                'id_gejala' => (int) ($master['id_gejala'] ?? 0),
            ];
        }

        return $gejala;
    }

    private function konversiZScoreDenganRuleBased($db, array $zscore): ?array
    {
        if (!$db->tableExists('tb_rule_based') || !$db->fieldExists('indikator', 'tb_rule_based')) {
            return null;
        }

        $rules = $db->table('tb_rule_based')
            ->where('aktif', 1)
            ->orderBy('urutan', 'ASC')
            ->orderBy('id_rule', 'ASC')
            ->get()
            ->getResultArray();

        if ($rules === []) {
            return null;
        }

        $kodeGejala = array_values(array_unique(array_filter(array_map(
            static fn ($rule) => (string) ($rule['kode_gejala'] ?? ''),
            $rules
        ))));
        $masterGejala = $this->getMasterGejalaByKode($db, $kodeGejala);
        $gejala = [];

        foreach ($zscore as $item) {
            $nilai = $item['nilai'] ?? null;
            if ($nilai === null || !is_numeric($nilai)) {
                continue;
            }

            $indicator = (string) ($item['label'] ?? '');
            $matchedRule = $this->getMatchedRule($rules, $indicator, (float) $nilai);
            if ($matchedRule === null || empty($matchedRule['kode_gejala'])) {
                continue;
            }

            $kode = (string) $matchedRule['kode_gejala'];
            $master = $masterGejala[$kode] ?? [];
            $kategori = (string) ($matchedRule['kategori_hasil'] ?? ($item['kategori'] ?? ''));
            $namaGejala = (string) ($master['nama_gejala'] ?? $matchedRule['nama_rule'] ?? $indicator);

            $gejala[] = [
                'kode' => $kode,
                'indikator' => $indicator,
                'nama' => $namaGejala . ' (' . $indicator . ' - ' . $kategori . ')',
                'kategori' => $kategori,
                'zscore' => (float) $nilai,
                'id_gejala' => (int) ($master['id_gejala'] ?? 0),
                'kode_rule' => (string) ($matchedRule['kode_rule'] ?? ''),
            ];
        }

        return $gejala;
    }

    private function getMatchedRule(array $rules, string $indicator, float $value): ?array
    {
        foreach ($rules as $rule) {
            if ((string) ($rule['indikator'] ?? '') !== $indicator) {
                continue;
            }

            if ($this->ruleMatchesValue($rule, $value)) {
                return $rule;
            }
        }

        return null;
    }

    private function ruleMatchesValue(array $rule, float $value): bool
    {
        $operatorBawah = (string) ($rule['operator_bawah'] ?? '');
        $operatorAtas = (string) ($rule['operator_atas'] ?? '');
        $batasBawah = $rule['batas_bawah'] ?? null;
        $batasAtas = $rule['batas_atas'] ?? null;

        if ($operatorBawah !== '' && $batasBawah !== null && $batasBawah !== '') {
            $batas = (float) $batasBawah;
            if ($operatorBawah === '>' && !($value > $batas)) {
                return false;
            }
            if ($operatorBawah === '>=' && !($value >= $batas)) {
                return false;
            }
        }

        if ($operatorAtas !== '' && $batasAtas !== null && $batasAtas !== '') {
            $batas = (float) $batasAtas;
            if ($operatorAtas === '<' && !($value < $batas)) {
                return false;
            }
            if ($operatorAtas === '<=' && !($value <= $batas)) {
                return false;
            }
        }

        return true;
    }

    private function getMasterGejalaByKode($db, array $kodeGejala): array
    {
        $kodeGejala = array_values(array_unique(array_filter($kodeGejala)));
        if ($kodeGejala === [] || !$db->tableExists('tb_gejala')) {
            return [];
        }

        $rows = $db->table('tb_gejala')
            ->select('id_gejala, kode_gejala, nama_gejala')
            ->whereIn('kode_gejala', $kodeGejala)
            ->get()
            ->getResultArray();

        $lookup = [];
        foreach ($rows as $row) {
            $lookup[(string) ($row['kode_gejala'] ?? '')] = $row;
        }

        return $lookup;
    }

    private function hitungNaiveBayes($db, array $gejala): array
    {
        if ($db->tableExists('tb_naive_bayes_prior') && $db->tableExists('tb_naive_bayes_likelihood')) {
            $configured = $this->hitungNaiveBayesFromEditableTables($db, $gejala);

            if ($configured !== null) {
                return $configured;
            }
        }

        return $this->fallbackBayes($gejala);
    }

    private function hitungNaiveBayesFromEditableTables($db, array $gejala): ?array
    {
        $priorRows = $db->table('tb_naive_bayes_prior')
            ->select('kelas, label, probabilitas, rekomendasi')
            ->get()
            ->getResultArray();

        if ($priorRows === []) {
            return null;
        }

        $likelihoodRows = $db->table('tb_naive_bayes_likelihood')
            ->select('indikator, kategori, kelas, probabilitas')
            ->get()
            ->getResultArray();

        $prior = [];
        $labels = [];
        foreach ($priorRows as $row) {
            $class = (string) ($row['kelas'] ?? '');
            $probability = (float) ($row['probabilitas'] ?? 0);

            if ($class === '' || $probability <= 0) {
                continue;
            }

            $prior[$class] = $probability;
            $labels[$class] = (string) ($row['label'] ?? ($this->kelas[$class] ?? $class));
        }

        if ($prior === []) {
            return null;
        }

        $likelihoodMap = [];
        foreach ($likelihoodRows as $row) {
            $class = (string) ($row['kelas'] ?? '');
            $indicator = (string) ($row['indikator'] ?? '');
            $category = (string) ($row['kategori'] ?? '');
            $probability = (float) ($row['probabilitas'] ?? 0);

            if ($class === '' || $indicator === '' || $category === '' || $probability <= 0) {
                continue;
            }

            $likelihoodMap[$class][$indicator][$category] = $probability;
        }

        [$gejalaLikelihoodMap, $gejalaLikelihoodCount] = $this->getGejalaLikelihoodMap($db);
        [$ruleLikelihoodMap, $ruleLikelihoodCount] = $this->getRuleBasedLikelihoodMap($db, array_keys($prior));

        $likelihood = [];
        $scores = [];
        foreach ($prior as $class => $priorProbability) {
            $logScore = log(max($priorProbability, 0.00001));

            foreach ($gejala as $item) {
                $indicator = (string) ($item['indikator'] ?? '');
                $category = (string) ($item['kategori'] ?? '');
                $kodeGejala = (string) ($item['kode'] ?? '');
                $evidenceKey = $kodeGejala !== '' ? $kodeGejala : trim($indicator . ' ' . $category);
                $probability = $likelihoodMap[$class][$indicator][$category]
                    ?? ($kodeGejala !== '' ? ($gejalaLikelihoodMap[$class][$kodeGejala] ?? null) : null)
                    ?? ($kodeGejala !== '' ? ($ruleLikelihoodMap[$class][$kodeGejala] ?? null) : null)
                    ?? 0.01;
                $likelihood[$class][$evidenceKey] = $probability;
                $logScore += log(max($probability, 0.00001));
            }

            $scores[] = [
                'kelas' => $class,
                'label' => $labels[$class] ?? ($this->kelas[$class] ?? $class),
                'skor' => $logScore,
                'prior' => $priorProbability,
                'posterior' => 0.0,
                'jumlah_data_latih' => count($likelihoodRows) + $gejalaLikelihoodCount + $ruleLikelihoodCount,
            ];
        }

        $scores = $this->normalizeScores($scores);

        return [
            'hasil' => $scores[0],
            'alternatif' => $scores,
            'prior' => $prior,
            'likelihood' => $likelihood,
            'jumlah_data_latih' => count($likelihoodRows) + $gejalaLikelihoodCount + $ruleLikelihoodCount,
        ];
    }

    private function getGejalaLikelihoodMap($db): array
    {
        if (!$db->tableExists('tb_nilai_probabilitas') || !$db->tableExists('tb_gejala')) {
            return [[], 0];
        }

        $rows = $db->table('tb_nilai_probabilitas np')
            ->select('g.kode_gejala, np.kode_hipotesis, np.nilai_probabilitas')
            ->join('tb_gejala g', 'g.id_gejala = np.id_gejala', 'left')
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $kodeGejala = (string) ($row['kode_gejala'] ?? '');
            $class = (string) ($row['kode_hipotesis'] ?? '');
            $probability = (float) ($row['nilai_probabilitas'] ?? 0);

            if ($kodeGejala === '' || $class === '' || $probability <= 0) {
                continue;
            }

            $map[$class][$kodeGejala] = min($probability, 1.0);
        }

        return [$map, count($rows)];
    }

    private function getRuleBasedLikelihoodMap($db, array $classes): array
    {
        if (
            !$db->tableExists('tb_rule_based')
            || !$db->fieldExists('kode_hipotesis', 'tb_rule_based')
            || !$db->fieldExists('kode_gejala', 'tb_rule_based')
        ) {
            return [[], 0];
        }

        $rows = $db->table('tb_rule_based')
            ->select('kode_hipotesis, kode_gejala')
            ->where('aktif', 1)
            ->get()
            ->getResultArray();

        if ($rows === []) {
            return [[], 0];
        }

        $gejalaCodes = array_values(array_unique(array_filter(array_map(
            static fn ($row) => (string) ($row['kode_gejala'] ?? ''),
            $rows
        ))));
        $map = [];

        foreach ($classes as $class) {
            foreach ($gejalaCodes as $kodeGejala) {
                $map[$class][$kodeGejala] = 0.10;
            }
        }

        foreach ($rows as $row) {
            $class = (string) ($row['kode_hipotesis'] ?? '');
            $kodeGejala = (string) ($row['kode_gejala'] ?? '');

            if ($class === '' || $kodeGejala === '') {
                continue;
            }

            $map[$class][$kodeGejala] = 0.90;
        }

        return [$map, count($rows)];
    }

    private function getTrainingData($db): array
    {
        if (!$db->tableExists('tb_anak_status_gizi')) {
            return [];
        }

        $rows = $db->table('tb_anak_status_gizi')
            ->select('bb_u, tb_u, bb_tb, zs_bb_u, zs_tb_u, zs_bb_tb')
            ->get()
            ->getResultArray();

        $training = [];
        foreach ($rows as $row) {
            $features = [
                'BB/U' => $this->kategoriFromStatusOrScore($row['bb_u'] ?? null, $row['zs_bb_u'] ?? null, 'bb_u'),
                'TB/U' => $this->kategoriFromStatusOrScore($row['tb_u'] ?? null, $row['zs_tb_u'] ?? null, 'tb_u'),
                'BB/TB' => $this->kategoriFromStatusOrScore($row['bb_tb'] ?? null, $row['zs_bb_tb'] ?? null, 'bb_tb'),
            ];

            if (array_filter($features) === []) {
                continue;
            }

            $training[] = [
                'kelas' => $this->kelasFromFeatures($features),
                'features' => array_filter($features),
            ];
        }

        return $training;
    }

    private function hitungNaiveBayesFromTraining(array $training, array $gejala): array
    {
        $classCounts = array_fill_keys(array_keys($this->kelas), 0);
        $featureCounts = [];
        $allFeatureValues = [];

        foreach ($training as $row) {
            $classCounts[$row['kelas']]++;

            foreach ($row['features'] as $feature => $value) {
                $featureCounts[$row['kelas']][$feature][$value] = ($featureCounts[$row['kelas']][$feature][$value] ?? 0) + 1;
                $allFeatureValues[$feature][$value] = true;
            }
        }

        $inputFeatures = [];
        foreach ($gejala as $item) {
            $inputFeatures[$item['indikator']] = $item['kategori'];
            $allFeatureValues[$item['indikator']][$item['kategori']] = true;
        }

        $total = count($training);
        $prior = [];
        $likelihood = [];
        $scores = [];

        foreach ($this->kelas as $class => $label) {
            $prior[$class] = ($classCounts[$class] + 1) / ($total + count($this->kelas));
            $logScore = log($prior[$class]);

            foreach ($inputFeatures as $feature => $value) {
                $options = max(count($allFeatureValues[$feature] ?? []), 1);
                $count = $featureCounts[$class][$feature][$value] ?? 0;
                $probability = ($count + 1) / ($classCounts[$class] + $options);
                $likelihood[$class][$feature] = $probability;
                $logScore += log($probability);
            }

            $scores[] = [
                'kelas' => $class,
                'label' => $label,
                'skor' => $logScore,
                'prior' => $prior[$class],
                'posterior' => 0.0,
                'jumlah_data_latih' => $classCounts[$class],
            ];
        }

        $scores = $this->normalizeScores($scores);

        return [
            'hasil' => $scores[0],
            'alternatif' => $scores,
            'prior' => $prior,
            'likelihood' => $likelihood,
            'jumlah_data_latih' => $total,
        ];
    }

    private function fallbackBayes(array $gejala): array
    {
        $score = ['H1' => 1.0, 'H2' => 1.0, 'H3' => 1.0];

        foreach ($gejala as $item) {
            $category = strtolower($item['kategori']);
            if (str_contains($category, 'sangat') || str_contains($category, 'buruk')) {
                $score['H1'] += 3.0;
            } elseif (str_contains($category, 'pendek') || str_contains($category, 'kurang')) {
                $score['H2'] += 2.0;
                $score['H1'] += 1.0;
            } else {
                $score['H3'] += 2.0;
            }
        }

        $scores = [];
        foreach ($this->kelas as $class => $label) {
            $scores[] = [
                'kelas' => $class,
                'label' => $label,
                'skor' => log($score[$class]),
                'prior' => 1 / 3,
                'posterior' => 0.0,
                'jumlah_data_latih' => 0,
            ];
        }

        return [
            'hasil' => $this->normalizeScores($scores)[0],
            'alternatif' => $this->normalizeScores($scores),
            'prior' => array_fill_keys(array_keys($this->kelas), 1 / 3),
            'likelihood' => [],
            'jumlah_data_latih' => 0,
        ];
    }

    private function normalizeScores(array $scores): array
    {
        $max = max(array_column($scores, 'skor'));
        $total = array_sum(array_map(static fn ($item) => exp($item['skor'] - $max), $scores));

        foreach ($scores as &$score) {
            $score['posterior'] = $total > 0 ? exp($score['skor'] - $max) / $total : 0;
            $score['posterior_persen'] = (int) round($score['posterior'] * 100);
        }
        unset($score);

        usort($scores, static fn ($a, $b) => $b['posterior'] <=> $a['posterior']);

        return $scores;
    }

    private function kelasFromFeatures(array $features): string
    {
        $joined = strtolower(implode(' ', $features));

        if (str_contains($joined, 'sangat pendek') || str_contains($joined, 'gizi buruk')) {
            return 'H1';
        }

        if (str_contains($joined, 'pendek') || str_contains($joined, 'kurang')) {
            return 'H2';
        }

        return 'H3';
    }

    private function kategoriFromStatusOrScore($status, $score, string $indicator): ?string
    {
        $status = trim((string) $status);
        if ($status !== '') {
            return ucwords(strtolower($status));
        }

        if ($score === null || $score === '' || !is_numeric($score)) {
            return null;
        }

        $score = (float) $score;

        return match ($indicator) {
            'bb_u' => $this->kategoriBbU($score),
            'tb_u' => $this->kategoriTbU($score),
            default => $this->kategoriBbTb($score),
        };
    }

    private function kategoriBbU(?float $score): string
    {
        if ($score === null) {
            return 'Tidak tersedia';
        }

        if ($score < -3) {
            return 'Berat badan sangat kurang';
        }

        if ($score < -2) {
            return 'Berat badan kurang';
        }

        if ($score > 1) {
            return 'Risiko berat badan lebih';
        }

        return 'Berat badan normal';
    }

    private function kategoriTbU(?float $score): string
    {
        if ($score === null) {
            return 'Tidak tersedia';
        }

        if ($score < -3) {
            return 'Sangat pendek';
        }

        if ($score < -2) {
            return 'Pendek';
        }

        if ($score > 3) {
            return 'Tinggi';
        }

        return 'Normal';
    }

    private function kategoriBbTb(?float $score): string
    {
        if ($score === null) {
            return 'Tidak tersedia';
        }

        if ($score < -3) {
            return 'Gizi buruk';
        }

        if ($score < -2) {
            return 'Gizi kurang';
        }

        if ($score > 2) {
            return 'Gizi lebih';
        }

        if ($score > 1) {
            return 'Berisiko gizi lebih';
        }

        return 'Gizi baik';
    }

    private function getRekomendasi(string $class): string
    {
        $db = db_connect();
        if ($db->tableExists('tb_naive_bayes_prior') && $db->fieldExists('rekomendasi', 'tb_naive_bayes_prior')) {
            $row = $db->table('tb_naive_bayes_prior')
                ->select('rekomendasi')
                ->where('kelas', $class)
                ->get()
                ->getRowArray();

            if (!empty($row['rekomendasi'])) {
                return (string) $row['rekomendasi'];
            }
        }

        return match ($class) {
            'H1' => 'Apabila anak terdiagnosis memiliki risiko stunting tinggi, maka diperlukan penanganan segera dengan berkonsultasi ke tenaga kesehatan. Orang tua perlu memperbaiki asupan gizi anak dengan memberikan makanan bergizi seimbang serta melakukan pemantauan pertumbuhan secara rutin agar kondisi tidak semakin memburuk.',
            'H2' => 'Jika anak berada pada kategori risiko stunting rendah, maka disarankan untuk meningkatkan kualitas pola makan dan menjaga keseimbangan nutrisi. Pemantauan pertumbuhan tetap perlu dilakukan secara berkala untuk mencegah peningkatan risiko.',
            default => 'Apabila anak anda berada dalam kondisi normal (tidak mengalami risiko stunting), maka orang tua perlu mempertahankan pola hidup sehat dengan memberikan asupan gizi seimbang serta melakukan pemantauan pertumbuhan secara rutin ke posyandu agar kondisi tetap optimal.',
        };
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

    private function meanStandardDeviation(array $values): array
    {
        $values = array_values(array_filter($values, static fn ($value) => $value !== null && $value !== '' && is_numeric($value)));

        if (count($values) < 2) {
            return [null, null];
        }

        $mean = array_sum($values) / count($values);
        $variance = 0.0;

        foreach ($values as $value) {
            $variance += ((float) $value - $mean) ** 2;
        }

        return [$mean, sqrt($variance / max(count($values) - 1, 1))];
    }

    private function simpanAnak(array &$hasil): void
    {
        $db = db_connect();
        if (!$db->tableExists('tb_anak')) {
            return;
        }

        $input = $hasil['input'];
        $zscore = $hasil['zscore'];
        $payload = [
            'nama_anak' => $input['nama'],
            'nik' => $input['nik'] ?: null,
            'jenis_kelamin' => $input['jenis_kelamin'],
            'tanggal_lahir' => $input['tanggal_lahir'] ?: null,
            'umur_bulan' => (int) $input['umur'],
            'berat_badan' => (float) $input['berat_badan'],
            'tinggi_badan' => (float) $input['tinggi_badan'],
            'lingkar_lengan' => $input['lingkar_lengan'] !== '' ? (float) $input['lingkar_lengan'] : null,
            'lingkar_kepala' => $input['lingkar_kepala'] !== '' ? (float) $input['lingkar_kepala'] : null,
            'nama_ortu' => $input['nama_ortu'] ?: null,
            'alamat' => $input['alamat'] ?: null,
            'riwayat_kehamilan' => $input['riwayat_kehamilan'] ?: null,
            'pola_makan' => $input['pola_makan'] ?: null,
            'tempat_tinggal' => $input['tempat_tinggal'] ?: null,
            'zs_bb_u' => $zscore['bb_u']['nilai'],
            'kategori_bb_u' => $zscore['bb_u']['kategori'],
            'zs_tb_u' => $zscore['tb_u']['nilai'],
            'kategori_tb_u' => $zscore['tb_u']['kategori'],
            'zs_bb_tb' => $zscore['bb_tb']['nilai'],
            'kategori_bb_tb' => $zscore['bb_tb']['kategori'],
            'gejala_zscore' => json_encode($hasil['gejala_terbaca'] ?? $hasil['gejala']),
        ];

        $payload = $this->filterExistingFields('tb_anak', $payload);
        $model = new AnakModel();
        $model->insert($payload);
        $hasil['id_anak'] = $model->getInsertID();
    }

    private function simpanHasilDiagnosa(array &$hasil): void
    {
        $db = db_connect();
        if (!$db->tableExists('tb_hasil_diagnosa')) {
            return;
        }

        $input = $hasil['input'];
        $zscore = $hasil['zscore'];
        $diagnosa = $hasil['diagnosa'] ?? [];
        $payload = [
            'id_anak' => $hasil['id_anak'] ?? null,
            'nama' => $input['nama'],
            'nik' => $input['nik'] ?: null,
            'jenis_kelamin' => $input['jenis_kelamin'],
            'tanggal_lahir' => $input['tanggal_lahir'] ?: null,
            'umur' => (int) $input['umur'],
            'berat_badan' => (float) $input['berat_badan'],
            'tinggi_badan' => (float) $input['tinggi_badan'],
            'lingkar_lengan' => $input['lingkar_lengan'] !== '' ? (float) $input['lingkar_lengan'] : null,
            'lingkar_kepala' => $input['lingkar_kepala'] !== '' ? (float) $input['lingkar_kepala'] : null,
            'riwayat_kehamilan' => $input['riwayat_kehamilan'] ?: null,
            'pola_makan' => $input['pola_makan'] ?: null,
            'tempat_tinggal' => $input['tempat_tinggal'] ?: null,
            'zs_bb_u' => $zscore['bb_u']['nilai'],
            'kategori_bb_u' => $zscore['bb_u']['kategori'],
            'zs_tb_u' => $zscore['tb_u']['nilai'],
            'kategori_tb_u' => $zscore['tb_u']['kategori'],
            'zs_bb_tb' => $zscore['bb_tb']['nilai'],
            'kategori_bb_tb' => $zscore['bb_tb']['kategori'],
            'gejala_zscore' => json_encode($hasil['gejala_terbaca'] ?? $hasil['gejala']),
            'kelas_hasil' => $diagnosa['kelas'] ?? null,
            'probabilitas_prior' => json_encode($hasil['prior']),
            'probabilitas_likelihood' => json_encode($hasil['likelihood']),
            'probabilitas_posterior' => json_encode($hasil['alternatif']),
            'rekomendasi' => $hasil['rekomendasi'],
            'id_kasus' => null,
            'nama_kasus' => ($diagnosa['kelas'] ?? 'H1') . ' - ' . ($diagnosa['label'] ?? 'Risiko Stunting Tinggi'),
            'persentase' => $hasil['persentase'],
            'jumlah_gejala' => (int) $hasil['jumlah_gejala'],
        ];

        $payload = $this->filterExistingFields('tb_hasil_diagnosa', $payload);
        $model = new HasilDiagnosaModel();
        $model->insert($payload);
        $hasil['id_hasil_diagnosa'] = $model->getInsertID();
    }

    private function filterExistingFields(string $table, array $payload): array
    {
        $fields = db_connect()->getFieldNames($table);

        return array_intersect_key($payload, array_flip($fields));
    }
}
