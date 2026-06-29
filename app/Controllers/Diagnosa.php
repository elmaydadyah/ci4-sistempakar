<?php

namespace App\Controllers;

use App\Models\AnakModel;
use App\Models\HasilDiagnosaModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Diagnosa extends BaseController
{
    private array $kelas = [
        'H1' => 'Risiko Stunting Tinggi',
        'H2' => 'Risiko Stunting Sedang',
        'H3' => 'Risiko Stunting Rendah',
    ];

    public function index()
    {
        helper(['form']);

        $data = [
            'hasil' => null,
            'old' => $this->emptyOldInput(),
            'errors' => [],
            'tb_gejala' => $this->getGejalaPertanyaan(),
            'riwayat' => $this->getRiwayatKonsultasiSession(),
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
                    $hasilId = (int) $data['hasil']['id_hasil_diagnosa'];
                    session()->set('last_hasil_diagnosa_id', $hasilId);
                    $this->simpanRiwayatKonsultasiSession($hasilId);
                    return redirect()->to('/konsultasi?hasil=' . $data['hasil']['id_hasil_diagnosa']);
                }
            }
        }

        return view('diagnosa/index', $data);
    }

    private function simpanRiwayatKonsultasiSession(int $hasilId): void
    {
        if ($hasilId <= 0) {
            return;
        }

        $riwayat = session()->get('riwayat_konsultasi');
        $riwayat = is_array($riwayat) ? array_map('intval', $riwayat) : [];
        array_unshift($riwayat, $hasilId);
        $riwayat = array_values(array_unique(array_filter($riwayat, static fn ($id) => $id > 0)));

        session()->set('riwayat_konsultasi', array_slice($riwayat, 0, 10));
    }

    private function getRiwayatKonsultasiSession(): array
    {
        $ids = session()->get('riwayat_konsultasi');
        $ids = is_array($ids) ? array_values(array_unique(array_filter(array_map('intval', $ids)))) : [];

        if ($ids === [] || !db_connect()->tableExists('tb_hasil_diagnosa')) {
            return [];
        }

        $rows = (new HasilDiagnosaModel())
            ->select('id_hasil_diagnosa, nama, umur, kelas_hasil, nama_kasus, persentase, created_at')
            ->whereIn('id_hasil_diagnosa', $ids)
            ->findAll();

        $rowsById = [];
        foreach ($rows as $row) {
            $rowsById[(int) ($row['id_hasil_diagnosa'] ?? 0)] = $row;
        }

        $riwayat = [];
        foreach ($ids as $id) {
            if (!isset($rowsById[$id])) {
                continue;
            }

            $riwayat[] = $rowsById[$id];
        }

        return $riwayat;
    }

    public function laporan(int $id)
    {
        $data = $this->getLaporanData($id);

        return view('diagnosa/laporan', $data);
    }

    public function downloadLaporan(int $id)
    {
        $data = $this->getLaporanData($id);
        $html = view('diagnosa/laporan', array_merge($data, ['download_mode' => true]));
        $nama = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) ($data['hasil']['nama'] ?? 'laporan'));
        $filename = 'laporan-hasil-diagnosa-' . trim($nama, '-') . '-' . $id . '.pdf';

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('chroot', FCPATH);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    private function getLaporanData(int $id): array
    {
        if ($id <= 0 || !db_connect()->tableExists('tb_hasil_diagnosa')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan tidak ditemukan.');
        }

        $row = (new HasilDiagnosaModel())->find($id);
        if (!$row) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Laporan tidak ditemukan.');
        }

        return [
            'hasil' => $this->formatStoredHasilDiagnosa($row),
            'row' => $row,
            'tanggal_cetak' => date('d/m/Y H:i'),
        ];
    }

    private function getHasilDiagnosaFromRequest(): ?array
    {
        $hasilId = (int) ($this->request->getGet('hasil') ?? 0);
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
        $storedClass = (string) ($row['kelas_hasil'] ?? '');
        $alternatifByKelas = $this->alternatifByKelas($alternatif);
        $diagnosa = ($storedClass !== '' && isset($alternatifByKelas[$storedClass])) ? $alternatifByKelas[$storedClass] : ($alternatif[0] ?? [
            'kelas' => $storedClass !== '' ? $storedClass : 'H1',
            'label' => $this->kelas[$storedClass] ?? 'Risiko Stunting Tinggi',
            'posterior_persen' => (float) ($row['persentase'] ?? 0),
            'posterior' => ((float) ($row['persentase'] ?? 0)) / 100,
        ]);
        if (!empty($diagnosa['kelas']) && isset($this->kelas[$diagnosa['kelas']])) {
            $diagnosa['label'] = $this->kelas[$diagnosa['kelas']];
        }

        $gejala = json_decode((string) ($row['gejala_zscore'] ?? '[]'), true);
        $gejala = is_array($gejala) ? $this->filterGejalaPendukung($gejala) : [];
        $gejalaAnak = $this->filterGejalaAnak($gejala);
        $penyebabTerbaca = $this->filterPenyebabKehamilan($gejala);
        $prior = json_decode((string) ($row['probabilitas_prior'] ?? '[]'), true);
        $likelihood = json_decode((string) ($row['probabilitas_likelihood'] ?? '[]'), true);
        $zscore = [
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
        ];

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
            'zscore' => $zscore,
            'gejala' => $gejala,
            'gejala_tambahan' => $gejala,
            'gejala_terbaca' => $gejala,
            'gejala_anak' => $gejalaAnak,
            'penyebab_terbaca' => $penyebabTerbaca,
            'risiko_obesitas' => $this->hasRisikoObesitas($zscore),
            'jumlah_gejala' => count($gejala),
            'diagnosa' => $diagnosa,
            'alternatif' => $alternatif,
            'prior' => is_array($prior) ? $prior : [],
            'likelihood' => is_array($likelihood) ? $likelihood : [],
            'jumlah_data_latih' => 0,
            'persentase' => (float) ($diagnosa['posterior_persen'] ?? ($row['persentase'] ?? 0)),
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
            'alamat' => (string) ($row['alamat'] ?? ''),
            'rt' => (string) ($row['rt'] ?? ''),
            'rw' => (string) ($row['rw'] ?? ''),
            'desa' => (string) ($row['desa'] ?? ''),
            'kelurahan' => (string) ($row['kelurahan'] ?? ''),
            'kecamatan' => (string) ($row['kecamatan'] ?? ''),
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
            'rt' => '',
            'rw' => '',
            'desa' => '',
            'kelurahan' => '',
            'kecamatan' => '',
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
            'rt' => (string) ($anak['rt'] ?? ''),
            'rw' => (string) ($anak['rw'] ?? ''),
            'desa' => (string) ($anak['desa'] ?? ''),
            'kelurahan' => (string) ($anak['kelurahan'] ?? ''),
            'kecamatan' => (string) ($anak['kecamatan'] ?? ''),
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
        $kelurahan = trim((string) $this->request->getPost('kelurahan'));

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
            'rt' => trim((string) $this->request->getPost('rt')),
            'rw' => trim((string) $this->request->getPost('rw')),
            'desa' => $kelurahan,
            'kelurahan' => $kelurahan,
            'kecamatan' => 'CILEUNGSI',
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

        if ($input['nik'] !== '' && !preg_match('/^[0-9]{16}$/', $input['nik'])) {
            $errors[] = 'NIK Harus berisikan 16 angka';
        }

        if (!in_array($input['jenis_kelamin'], ['L', 'P'], true)) {
            $errors[] = 'Jenis kelamin wajib dipilih.';
        }

        if ($input['tanggal_lahir'] === '') {
            $errors[] = 'Tanggal lahir wajib diisi.';
        } elseif (!$this->isValidBirthDate($input['tanggal_lahir'])) {
            $errors[] = 'Tanggal lahir tidak valid.';
        }

        if ($input['umur'] === '' || !ctype_digit($input['umur']) || (int) $input['umur'] < 0 || (int) $input['umur'] > 60) {
            $errors[] = 'Umur balita harus 0 sampai 60 bulan.';
        }

        if ($input['nama_ortu'] === '') {
            $errors[] = 'Nama ibu/ayah wajib diisi.';
        }

        foreach ([
            'alamat' => 'Alamat lengkap',
            'rt' => 'RT',
            'rw' => 'RW',
            'kelurahan' => 'Desa/Kel',
            'kecamatan' => 'Kecamatan',
        ] as $field => $label) {
            if ($input[$field] === '') {
                $errors[] = $label . ' wajib diisi.';
            }
        }

        if ($input['kelurahan'] !== '' && !in_array($input['kelurahan'], $this->getKelurahanOptions(), true)) {
            $errors[] = 'Desa/Kel wajib dipilih dari daftar.';
        }

        foreach (['rt' => 'RT', 'rw' => 'RW'] as $field => $label) {
            if ($input[$field] !== '' && !preg_match('/^[0-9]+$/', $input[$field])) {
                $errors[] = $label . ' wajib diisi angka.';
            }
        }

        foreach (['berat_badan' => 'Berat badan', 'tinggi_badan' => 'Tinggi badan'] as $field => $label) {
            if ($input[$field] === '' || !is_numeric($input[$field]) || (float) $input[$field] <= 0) {
                $errors[] = $label . ' wajib diisi dengan angka lebih dari 0.';
            }
        }

        foreach (['lingkar_lengan' => 'Lingkar lengan', 'lingkar_kepala' => 'Lingkar kepala'] as $field => $label) {
            if ($input[$field] === '' || !is_numeric($input[$field]) || (float) $input[$field] <= 0) {
                $errors[] = $label . ' wajib diisi dengan angka lebih dari 0.';
            }
        }

        foreach ($this->getGejalaPertanyaan((int) $input['umur']) as $gejala) {
            $idGejala = (int) ($gejala['id_gejala'] ?? 0);
            if ($idGejala <= 0) {
                continue;
            }

            if (!in_array($input['jawaban_gejala'][$idGejala] ?? null, ['ya', 'tidak'], true)) {
                $errors[] = 'Semua pertanyaan gejala wajib dijawab.';
                break;
            }
        }

        return $errors;
    }

    private function isValidBirthDate(string $date): bool
    {
        $birthDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
        $errors = \DateTimeImmutable::getLastErrors();

        if (!$birthDate || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return false;
        }

        return $birthDate <= new \DateTimeImmutable('today');
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
        $gejala = [];
        $gejalaTambahan = $this->gejalaTambahanDariJawaban($db, $input['jawaban_gejala'] ?? [], (int) $input['umur']);
        $gejalaTerbaca = array_merge($gejala, $gejalaTambahan);
        $gejalaAnak = $this->filterGejalaAnak($gejalaTerbaca);
        $penyebabTerbaca = $this->filterPenyebabKehamilan($gejalaTerbaca);
        $bayesZScore = $this->hitungTeoremaBayes($db, $this->zscoreMenjadiEvidence($zscore));
        $bayesGejala = $this->hitungTeoremaBayes($db, $gejalaTambahan);
        $bayes = $this->gabungkanBayesBerbobot($bayesZScore, $bayesGejala, 0.30, 0.70);
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
            'gejala_anak' => $gejalaAnak,
            'penyebab_terbaca' => $penyebabTerbaca,
            'risiko_obesitas' => $this->hasRisikoObesitas($zscore),
            'jumlah_gejala' => count($gejalaTerbaca),
            'diagnosa' => $diagnosa,
            'alternatif' => $bayes['alternatif'],
            'prior' => $bayes['prior'],
            'likelihood' => $bayes['likelihood'],
            'jumlah_data_latih' => $bayes['jumlah_data_latih'],
            'persentase' => round($posterior * 100, 2),
            'rekomendasi' => $this->getRekomendasi($diagnosa['kelas'] ?? 'H1'),
        ];
    }

    private function filterGejalaAnak(array $gejala): array
    {
        return array_values(array_filter($gejala, fn ($item) => !$this->isPenyebabKehamilan($item)));
    }

    private function filterPenyebabKehamilan(array $gejala): array
    {
        return array_values(array_filter($gejala, fn ($item) => $this->isPenyebabKehamilan($item)));
    }

    private function filterGejalaPendukung(array $gejala): array
    {
        return array_values(array_filter($gejala, function ($item) {
            if (!is_array($item)) {
                return false;
            }

            return !in_array(strtoupper((string) ($item['kode'] ?? '')), ['G01', 'G02', 'G11', 'G011'], true);
        }));
    }

    private function isPenyebabKehamilan($gejala): bool
    {
        if (!is_array($gejala)) {
            return false;
        }

        return in_array((string) ($gejala['kode'] ?? ''), ['G15', 'G16', 'G17', 'G18', 'G19'], true);
    }

    private function hasRisikoObesitas(array $zscore): bool
    {
        foreach ($zscore as $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = (string) ($item['label'] ?? '');
            $kategori = (string) ($item['kategori'] ?? '');

            if ($label === 'BB/U' && $kategori === 'Risiko berat badan lebih') {
                return true;
            }

            if ($label === 'BB/TB' && in_array($kategori, ['Berisiko gizi lebih', 'Gizi lebih'], true)) {
                return true;
            }
        }

        return false;
    }

    private function getGejalaPertanyaan(?int $umurBulan = null): array
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

        $rows = array_filter($rows, function ($row) use ($umurBulan) {
            if ($umurBulan === null) {
                return true;
            }

            return $this->gejalaBerlakuUntukUmur((string) ($row['kode_gejala'] ?? ''), $umurBulan);
        });

        return array_map(function ($row) use ($umurBulan) {
            $kodeGejala = (string) ($row['kode_gejala'] ?? '');
            $row['pertanyaan_gejala'] = $this->pertanyaanGejala(
                $kodeGejala,
                (string) ($row['nama_gejala'] ?? ''),
                $umurBulan
            );

            $row['umur_min'] = $this->umurMinGejala($kodeGejala);
            $row['umur_max'] = $this->umurMaxGejala($kodeGejala);

            return $row;
        }, array_values($rows));
    }

    private function pertanyaanGejala(string $kodeGejala, string $namaGejala, ?int $umurBulan = null): string
    {
        if ($kodeGejala === 'G06') {
            return $this->pertanyaanGejalaG06($umurBulan);
        }

        $pertanyaan = [
            'G01' => 'Apakah berat badan anak cenderung kurang atau tidak sesuai dengan anak seusianya?',
            'G02' => 'Apakah tinggi badan anak lebih pendek atau rendah dari standar anak seusianya?',
            'G03' => 'Apakah anak terlihat kurang aktif?',
            'G04' => 'Apakah daya tahan tubuh anak rendah atau anak sering sakit?',
            'G05' => 'Apakah anak mengalami keterlambatan bicara?',
            'G06' => 'Apakah perkembangan keterampilan fisik anak lambat, seperti berguling, duduk, berdiri, atau berjalan?',
            'G07' => 'Apakah anak susah fokus saat bermain, belajar, atau berinteraksi?',
            'G08' => 'Apakah gigi susu anak terlambat tumbuh?',
            'G09' => 'Apakah anak mendapat pengukuran tinggi dan berat badan rutin sebanyak 8x dalam setahun di posyandu/klinik bidan dan dicatat di buku KMS?',
            'G10' => 'Apakah nafsu makan anak berkurang?',
            'G11' => 'Apakah anak terlihat mengalami kekurangan gizi?',
            'G12' => 'Apakah berat badan anak cenderung tetap atau menurun?',
            'G13' => 'Apakah anak mendapatkan imunisasi dasar lengkap seperti HB 0, BCG, Polio, DPT, dan Campak?',
            'G14' => 'Apakah anak rutin mendapatkan obat kecacingan 2 kali setahun?',
            'G15' => 'Apakah ibu memiliki HB kurang dari 11 selama masa kehamilan?',
            'G16' => 'Apakah selama hamil ibu mengalami mual muntah setelah trimester 1 atau lebih dari trimester 1?',
            'G17' => 'Apakah ibu sering merasa lemas, pusing, dan mudah lelah saat hamil?',
            'G18' => 'Apakah ibu mengalami penurunan atau kenaikan berat badan tidak normal saat hamil?',
            'G19' => 'Apakah ibu memiliki lingkar lengan atas kurang dari 23,5 cm?',
            'G20' => 'Apakah keluarga memiliki akses jamban sehat, seperti jenis leher angsa atau septic tank?',
            'G21' => 'Apakah keluarga memiliki akses air bersih di rumah?',
        ];

        if (isset($pertanyaan[$kodeGejala])) {
            return $pertanyaan[$kodeGejala];
        }

        return 'Apakah anak mengalami ' . strtolower($namaGejala) . '?';
    }

    private function pertanyaanGejalaG06(?int $umurBulan): string
    {
        if ($umurBulan === null) {
            return 'Apakah perkembangan keterampilan fisik anak lambat, seperti berguling, duduk, berdiri, atau berjalan?';
        }

        if ($umurBulan >= 4 && $umurBulan <= 5) {
            return 'Apakah anak belum mampu menahan kepala dengan stabil saat digendong atau belum mampu mengangkat tubuh saat tengkurap?';
        }

        if ($umurBulan >= 6 && $umurBulan <= 8) {
            return 'Apakah anak belum mampu berguling atau belum mampu duduk dengan bantuan/tumpuan tangan?';
        }

        if ($umurBulan >= 9 && $umurBulan <= 11) {
            return 'Apakah anak belum mampu duduk tanpa bantuan atau belum mulai belajar merangkak/berdiri dengan bantuan?';
        }

        if ($umurBulan >= 12 && $umurBulan <= 14) {
            return 'Apakah anak belum mampu berdiri dengan berpegangan atau berjalan sambil berpegangan pada benda?';
        }

        if ($umurBulan >= 15 && $umurBulan <= 17) {
            return 'Apakah anak belum mampu berjalan beberapa langkah sendiri?';
        }

        return 'Apakah anak belum mampu berjalan tanpa bantuan atau mengalami kesulitan gerak sesuai usianya?';
    }

    private function gejalaBerlakuUntukUmur(string $kodeGejala, int $umurBulan): bool
    {
        return $umurBulan >= $this->umurMinGejala($kodeGejala) && $umurBulan <= $this->umurMaxGejala($kodeGejala);
    }

    private function umurMinGejala(string $kodeGejala): int
    {
        return match ($kodeGejala) {
            'G05' => 12,
            'G06', 'G14' => 13,
            'G07', 'G09' => 25,
            'G08' => 7,
            default => 0,
        };
    }

    private function umurMaxGejala(string $kodeGejala): int
    {
        return match ($kodeGejala) {
            'G05', 'G06', 'G08', 'G09', 'G14' => 60,
            default => 60,
        };
    }

    private function gejalaTambahanDariJawaban($db, array $jawabanGejala, int $umurBulan): array
    {
        $ids = array_values(array_filter(array_map('intval', array_keys($jawabanGejala)), static fn ($id) => $id > 0));

        if ($ids === [] || !$db->tableExists('tb_gejala')) {
            return [];
        }

        $rows = $db->table('tb_gejala')
            ->select('id_gejala, kode_gejala, nama_gejala')
            ->whereIn('id_gejala', $ids)
            ->orderBy('id_gejala', 'ASC')
            ->get()
            ->getResultArray();

        $gejala = [];
        foreach ($rows as $row) {
            $idGejala = (int) ($row['id_gejala'] ?? 0);
            $kodeGejala = (string) ($row['kode_gejala'] ?? ('G' . $idGejala));
            $jawaban = (string) ($jawabanGejala[$idGejala] ?? '');

            if (!$this->gejalaBerlakuUntukUmur($kodeGejala, $umurBulan)) {
                continue;
            }

            if (!$this->jawabanGejalaMasukHitungan($kodeGejala, $jawaban)) {
                continue;
            }

            $gejala[] = [
                'kode' => $kodeGejala,
                'indikator' => 'Pertanyaan gejala',
                'nama' => (string) ($row['nama_gejala'] ?? ''),
                'kategori' => ucfirst($jawaban),
                'id_gejala' => $idGejala,
            ];
        }

        return $gejala;
    }

    private function jawabanGejalaMasukHitungan(string $kodeGejala, string $jawaban): bool
    {
        $kodeDenganRisikoJikaTidak = ['G09', 'G13', 'G14', 'G20', 'G21'];

        if (in_array($kodeGejala, $kodeDenganRisikoJikaTidak, true)) {
            return $jawaban === 'tidak';
        }

        return $jawaban === 'ya';
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
            if (!$this->bolehMasukGejalaZScore($kode, (string) ($item['label'] ?? ''), $kategori, $item['nilai'] ?? null)) {
                continue;
            }

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
            if (!$this->bolehMasukGejalaZScore($kode, $indicator, $kategori, $nilai)) {
                continue;
            }

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

    private function bolehMasukGejalaZScore(string $kodeGejala, string $indikator, string $kategori, $nilai): bool
    {
        $kodeGejala = strtoupper($kodeGejala);
        $indikator = strtoupper(trim($indikator));
        $kategori = strtolower(trim($kategori));
        $nilai = is_numeric($nilai) ? (float) $nilai : null;

        if ($kodeGejala === 'G01' && $indikator === 'BB/U') {
            return in_array($kategori, ['berat badan sangat kurang', 'berat badan kurang'], true)
                || ($nilai !== null && $nilai < -2);
        }

        if (in_array($kodeGejala, ['G11', 'G011'], true) && $indikator === 'BB/TB') {
            return in_array($kategori, ['gizi buruk', 'gizi kurang'], true)
                || ($nilai !== null && $nilai < -2);
        }

        return true;
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

    private function zscoreMenjadiEvidence(array $zscore): array
    {
        $evidence = [];

        foreach ($zscore as $item) {
            if (($item['nilai'] ?? null) === null) {
                continue;
            }

            $label = (string) ($item['label'] ?? 'Z-Score');
            $kategori = (string) ($item['kategori'] ?? 'Tidak tersedia');

            if ($kategori === '' || strtolower($kategori) === 'tidak tersedia') {
                continue;
            }

            $evidence[] = [
                'kode' => '',
                'indikator' => $label,
                'nama' => $label . ' - ' . $kategori,
                'kategori' => $kategori,
                'zscore' => $item['nilai'],
            ];
        }

        return $evidence;
    }

    private function gabungkanBayesBerbobot(array $bayesZScore, array $bayesGejala, float $bobotZScore, float $bobotGejala): array
    {
        $alternatifZScore = $this->alternatifByKelas($bayesZScore['alternatif'] ?? []);
        $alternatifGejala = $this->alternatifByKelas($bayesGejala['alternatif'] ?? []);
        $classes = array_values(array_unique(array_merge(array_keys($this->kelas), array_keys($alternatifZScore), array_keys($alternatifGejala))));
        $totalBobot = $bobotZScore + $bobotGejala;

        if ($totalBobot <= 0) {
            $bobotZScore = 0.30;
            $bobotGejala = 0.70;
            $totalBobot = 1.0;
        }

        $bobotZScore /= $totalBobot;
        $bobotGejala /= $totalBobot;

        $scores = [];
        foreach ($classes as $class) {
            $zscorePosterior = (float) ($alternatifZScore[$class]['posterior'] ?? 0.0);
            $gejalaPosterior = (float) ($alternatifGejala[$class]['posterior'] ?? 0.0);
            $posterior = ($zscorePosterior * $bobotZScore) + ($gejalaPosterior * $bobotGejala);

            $scores[] = [
                'kelas' => $class,
                'label' => $this->kelas[$class] ?? ($alternatifZScore[$class]['label'] ?? ($alternatifGejala[$class]['label'] ?? $class)),
                'skor' => $posterior,
                'prior' => $posterior,
                'posterior' => $posterior,
                'posterior_persen' => round($posterior * 100, 2),
                'posterior_zscore' => $zscorePosterior,
                'posterior_zscore_persen' => round($zscorePosterior * 100, 2),
                'posterior_gejala' => $gejalaPosterior,
                'posterior_gejala_persen' => round($gejalaPosterior * 100, 2),
                'bobot_zscore' => $bobotZScore,
                'bobot_gejala' => $bobotGejala,
                'jumlah_data_latih' => max(
                    (int) ($alternatifZScore[$class]['jumlah_data_latih'] ?? 0),
                    (int) ($alternatifGejala[$class]['jumlah_data_latih'] ?? 0)
                ),
            ];
        }

        usort($scores, static fn ($a, $b) => $b['posterior'] <=> $a['posterior']);

        return [
            'hasil' => $scores[0] ?? [
                'kelas' => 'H1',
                'label' => $this->kelas['H1'],
                'posterior' => 0.0,
                'posterior_persen' => 0.0,
            ],
            'alternatif' => $scores,
            'prior' => [
                'zscore' => $bayesZScore['prior'] ?? [],
                'gejala' => $bayesGejala['prior'] ?? [],
                'bobot' => ['zscore' => $bobotZScore, 'gejala' => $bobotGejala],
            ],
            'likelihood' => [
                'zscore' => $bayesZScore['likelihood'] ?? [],
                'gejala' => $bayesGejala['likelihood'] ?? [],
            ],
            'jumlah_data_latih' => max((int) ($bayesZScore['jumlah_data_latih'] ?? 0), (int) ($bayesGejala['jumlah_data_latih'] ?? 0)),
        ];
    }

    private function alternatifByKelas(array $alternatif): array
    {
        $lookup = [];

        foreach ($alternatif as $item) {
            if (!is_array($item)) {
                continue;
            }

            $class = (string) ($item['kelas'] ?? '');
            if ($class === '') {
                continue;
            }

            $lookup[$class] = $item;
        }

        return $lookup;
    }

    private function hitungTeoremaBayes($db, array $gejala): array
    {
        if ($this->getTeoremaBayesTable($db, 'prior') !== null && $this->getTeoremaBayesTable($db, 'likelihood') !== null) {
            $configured = $this->hitungTeoremaBayesFromEditableTables($db, $gejala);

            if ($configured !== null) {
                return $configured;
            }
        }

        return $this->fallbackBayes($gejala);
    }

    private function hitungTeoremaBayesFromEditableTables($db, array $gejala): ?array
    {
        $priorTable = $this->getTeoremaBayesTable($db, 'prior');
        $likelihoodTable = $this->getTeoremaBayesTable($db, 'likelihood');

        if ($priorTable === null || $likelihoodTable === null) {
            return null;
        }

        $priorRows = $db->table($priorTable)
            ->select('kelas, label, probabilitas, rekomendasi')
            ->get()
            ->getResultArray();

        if ($priorRows === []) {
            return null;
        }

        $likelihoodRows = $db->table($likelihoodTable)
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
                $probability = ($kodeGejala !== '' ? ($gejalaLikelihoodMap[$class][$kodeGejala] ?? null) : null)
                    ?? ($likelihoodMap[$class][$indicator][$category] ?? null)
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

    private function hitungTeoremaBayesFromTraining(array $training, array $gejala): array
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
            $score['posterior_persen'] = round($score['posterior'] * 100, 2);
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
        $priorTable = $this->getTeoremaBayesTable($db, 'prior');
        if ($priorTable !== null && $db->fieldExists('rekomendasi', $priorTable)) {
            $row = $db->table($priorTable)
                ->select('rekomendasi')
                ->where('kelas', $class)
                ->get()
                ->getRowArray();

            if (!empty($row['rekomendasi'])) {
                return (string) $row['rekomendasi'];
            }
        }

        return match ($class) {
            'H1' => 'Apabila anak berada pada kategori risiko stunting tinggi, segera konsultasikan ke tenaga kesehatan atau puskesmas. Perbaiki asupan gizi anak dan lakukan pemantauan pertumbuhan secara rutin.',
            'H2' => 'Jika anak berada pada kategori risiko stunting sedang, maka disarankan untuk meningkatkan kualitas pola makan, menjaga keseimbangan nutrisi, dan melakukan konsultasi ke posyandu atau tenaga kesehatan untuk pemantauan lanjutan.',
            default => 'Jika anak berada pada kategori risiko stunting rendah, tetap jaga pola makan bergizi seimbang dan lakukan pemantauan pertumbuhan secara berkala di posyandu.',
        };
    }

    private function getTeoremaBayesTable($db, string $type): ?string
    {
        $tables = match ($type) {
            'prior' => ['tb_teorema_bayes_prior', 'tb_naive_bayes_prior'],
            'likelihood' => ['tb_teorema_bayes_likelihood', 'tb_naive_bayes_likelihood'],
            default => [],
        };

        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                return $table;
            }
        }

        return null;
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
            'rt' => $input['rt'] ?: null,
            'rw' => $input['rw'] ?: null,
            'desa' => $input['desa'] ?: null,
            'kelurahan' => $input['kelurahan'] ?: null,
            'kecamatan' => $input['kecamatan'] ?: null,
            'riwayat_kehamilan' => $input['riwayat_kehamilan'] ?: null,
            'pola_makan' => $input['pola_makan'] ?: null,
            'tempat_tinggal' => $input['tempat_tinggal'] ?: $this->formatTempatTinggal($input),
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
            'alamat' => $input['alamat'] ?: null,
            'rt' => $input['rt'] ?: null,
            'rw' => $input['rw'] ?: null,
            'desa' => $input['desa'] ?: null,
            'kelurahan' => $input['kelurahan'] ?: null,
            'kecamatan' => $input['kecamatan'] ?: null,
            'riwayat_kehamilan' => $input['riwayat_kehamilan'] ?: null,
            'pola_makan' => $input['pola_makan'] ?: null,
            'tempat_tinggal' => $input['tempat_tinggal'] ?: $this->formatTempatTinggal($input),
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

    private function getKelurahanOptions(): array
    {
        return [
            'Cileungsi',
            'Cileungsi Kidul',
            'Cipenjo',
            'Cipeucang',
            'Dayeuh',
            'Gandoang',
            'Jatisari',
            'Limus Nunggal',
            'Mampir',
            'Mekarsari',
            'Pasir Angin',
            'Situsari',
        ];
    }

    private function formatTempatTinggal(array $input): ?string
    {
        $parts = array_filter([
            $input['desa'] ?? '',
            $input['kelurahan'] ?? '',
            $input['kecamatan'] ?? '',
        ], static fn ($value) => trim((string) $value) !== '');

        return $parts === [] ? null : implode(', ', $parts);
    }
}
