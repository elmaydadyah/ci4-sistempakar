<?php

namespace App\Controllers;

use App\Models\AnakModel;

class Home extends BaseController
{
    public function index(): string
    {
        return view('layout/landing/header')
            . view('layout/landing/navbar')
            . '<main class="landing-shell">'
            . view('layout/landing/content')
            . view('layout/landing/about')
            . view('layout/landing/features', ['articles' => $this->getArticles()])
            . view('layout/landing/contact')
            . view('layout/landing/faq')
            . view('layout/landing/footer');
    }

    public function artikel(string $slug)
    {
        $articles = $this->getArticles();

        if (!isset($articles[$slug])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Artikel tidak ditemukan.');
        }

        return view('layout/landing/header')
            . view('layout/landing/navbar')
            . '<main class="landing-shell article-shell">'
            . view('layout/landing/article', [
                'article' => $articles[$slug],
                'articles' => $articles,
            ])
            . view('layout/landing/footer');
    }

    public function konseling()
    {
        return redirect()->to('/konsultasi');
    }

    public function storeAnak()
    {
        $rules = [
            'nama_anak' => 'permit_empty|min_length[2]|max_length[150]',
            'nama' => 'permit_empty|min_length[2]|max_length[150]',
            'nik' => 'permit_empty|numeric|min_length[8]|max_length[32]',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tanggal_lahir' => 'permit_empty|valid_date[Y-m-d]',
            'umur_bulan' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[60]',
            'umur' => 'permit_empty|integer|greater_than_equal_to[0]|less_than_equal_to[60]',
            'berat_badan' => 'required|decimal',
            'tinggi_badan' => 'required|decimal',
            'lingkar_lengan' => 'permit_empty|decimal',
            'lingkar_kepala' => 'permit_empty|decimal',
            'nama_ortu' => 'permit_empty|max_length[150]',
            'tempat_tinggal' => 'permit_empty|max_length[150]',
            'alamat' => 'permit_empty',
        ];

        $namaAnak = trim((string) ($this->request->getPost('nama_anak') ?: $this->request->getPost('nama')));
        $umurBulan = (string) ($this->request->getPost('umur_bulan') ?: $this->request->getPost('umur'));
        $old = [
            'nama_anak' => $namaAnak,
            'nama' => $namaAnak,
            'nik' => trim((string) $this->request->getPost('nik')),
            'jenis_kelamin' => (string) $this->request->getPost('jenis_kelamin'),
            'tanggal_lahir' => (string) $this->request->getPost('tanggal_lahir'),
            'umur_bulan' => $umurBulan,
            'umur' => $umurBulan,
            'berat_badan' => (string) $this->request->getPost('berat_badan'),
            'tinggi_badan' => (string) $this->request->getPost('tinggi_badan'),
            'lingkar_lengan' => (string) $this->request->getPost('lingkar_lengan'),
            'lingkar_kepala' => (string) $this->request->getPost('lingkar_kepala'),
            'nama_ortu' => trim((string) $this->request->getPost('nama_ortu')),
            'tempat_tinggal' => trim((string) $this->request->getPost('tempat_tinggal')),
            'alamat' => trim((string) $this->request->getPost('alamat')),
            'riwayat_kehamilan' => trim((string) $this->request->getPost('riwayat_kehamilan')),
            'pola_makan' => trim((string) $this->request->getPost('pola_makan')),
        ];

        if ($old['nama_anak'] === '') {
            return redirect()->to('/konseling')
                ->with('konseling_errors', ['Nama anak wajib diisi.'])
                ->with('konseling_old', $old);
        }

        if (!$this->validate($rules)) {
            return redirect()->to('/konseling')
                ->with('konseling_errors', $this->validator->getErrors())
                ->with('konseling_old', $old);
        }

        $model = new AnakModel();
        $payload = [
            'nama_anak' => $old['nama_anak'],
            'nik' => $old['nik'] !== '' ? $old['nik'] : null,
            'jenis_kelamin' => $old['jenis_kelamin'],
            'tanggal_lahir' => $old['tanggal_lahir'] !== '' ? $old['tanggal_lahir'] : null,
            'umur_bulan' => (int) $old['umur_bulan'],
            'berat_badan' => $old['berat_badan'] !== '' ? (float) $old['berat_badan'] : null,
            'tinggi_badan' => $old['tinggi_badan'] !== '' ? (float) $old['tinggi_badan'] : null,
            'lingkar_lengan' => $old['lingkar_lengan'] !== '' ? (float) $old['lingkar_lengan'] : null,
            'lingkar_kepala' => $old['lingkar_kepala'] !== '' ? (float) $old['lingkar_kepala'] : null,
            'nama_ortu' => $old['nama_ortu'] !== '' ? $old['nama_ortu'] : null,
            'alamat' => $old['alamat'] !== '' ? $old['alamat'] : null,
            'tempat_tinggal' => $old['tempat_tinggal'] !== '' ? $old['tempat_tinggal'] : null,
            'riwayat_kehamilan' => $old['riwayat_kehamilan'] !== '' ? $old['riwayat_kehamilan'] : null,
            'pola_makan' => $old['pola_makan'] !== '' ? $old['pola_makan'] : null,
        ];

        $payload = array_intersect_key($payload, array_flip(db_connect()->getFieldNames('tb_anak')));
        $model->insert($payload);

        return redirect()->to('/konsultasi?anak=' . $model->getInsertID());
    }

    private function getArticles(): array
    {
        return [
            'mengenal-stunting-pada-balita' => [
                'title' => 'Mengenal Stunting pada Balita',
                'category' => 'Stunting',
                'image' => 'assets/images/landing/puskesmas2.jpeg',
                'excerpt' => 'Pahami apa itu stunting, mengapa perlu dicegah sejak dini, dan bagaimana keluarga dapat memantau pertumbuhan anak.',
                'date' => '05 Jun, 2026',
                'body' => [
                    'Stunting adalah kondisi ketika pertumbuhan anak terhambat sehingga tinggi badan tidak sesuai dengan standar usianya. Kondisi ini biasanya berkaitan dengan asupan gizi, kesehatan ibu dan anak, infeksi berulang, serta lingkungan tempat tinggal.',
                    'Pemantauan tinggi dan berat badan secara rutin penting dilakukan karena perubahan pertumbuhan anak sering terlihat dari data pengukuran. Jika ditemukan tanda pertumbuhan tidak sesuai, keluarga dapat segera berkonsultasi ke posyandu atau puskesmas.',
                    'StuntCare membantu orang tua melakukan skrining awal. Hasil sistem bukan pengganti diagnosis dokter, tetapi dapat menjadi bahan awal untuk menentukan apakah anak perlu dipantau lebih lanjut.',
                ],
            ],
            'penyebab-stunting' => [
                'title' => 'Penyebab Stunting',
                'category' => 'Faktor Risiko',
                'image' => 'assets/images/landing/about1.png',
                'excerpt' => 'Kenali faktor yang dapat meningkatkan risiko stunting, mulai dari gizi, riwayat kehamilan, sampai sanitasi rumah.',
                'date' => '05 Jun, 2026',
                'body' => [
                    'Penyebab stunting tidak hanya satu. Faktor yang sering berpengaruh antara lain asupan gizi yang kurang, infeksi berulang, riwayat kehamilan ibu, serta kurangnya pemantauan pertumbuhan anak.',
                    'Lingkungan rumah juga dapat memengaruhi kesehatan anak. Akses air bersih, jamban sehat, kebersihan makanan, dan pola pengasuhan ikut berperan dalam mendukung tumbuh kembang balita.',
                    'Karena penyebabnya beragam, pencegahan stunting perlu dilakukan bersama. Orang tua, kader posyandu, dan tenaga kesehatan dapat saling membantu memantau kondisi anak sejak dini.',
                ],
            ],
            '1000-hpk-masa-penting-cegah-stunting' => [
                'title' => '1000 HPK: Masa Penting Cegah Stunting',
                'category' => 'Pencegahan',
                'image' => 'assets/images/landing/foto.png',
                'excerpt' => 'Seribu hari pertama kehidupan adalah masa penting untuk memenuhi gizi dan menjaga kesehatan ibu serta anak.',
                'date' => '05 Jun, 2026',
                'body' => [
                    '1000 HPK adalah masa sejak awal kehamilan sampai anak berusia sekitar dua tahun. Pada masa ini, pertumbuhan otak dan tubuh anak berlangsung sangat cepat sehingga kebutuhan gizi perlu diperhatikan.',
                    'Upaya pencegahan dapat dimulai dari pemeriksaan kehamilan, konsumsi makanan bergizi, pemberian ASI, MPASI yang sesuai usia, imunisasi, dan pemantauan tumbuh kembang di posyandu.',
                    'Jika orang tua melihat tanda anak sulit naik berat badan, tinggi badan tidak bertambah sesuai usia, atau sering sakit, sebaiknya segera berkonsultasi dengan tenaga kesehatan.',
                ],
            ],
            'ciri-ciri-anak-berisiko-stunting' => [
                'title' => 'Ciri-Ciri Anak Berisiko Stunting',
                'category' => 'Gejala',
                'image' => 'assets/images/landing/puskesmas.jpeg',
                'excerpt' => 'Beberapa tanda awal dapat membantu orang tua lebih cepat mengenali risiko stunting pada anak.',
                'date' => '05 Jun, 2026',
                'body' => [
                    'Anak yang berisiko stunting dapat menunjukkan beberapa tanda, seperti tinggi badan lebih pendek dari anak seusianya, berat badan sulit naik, kurang aktif, atau sering mengalami sakit.',
                    'Tanda lain yang perlu diperhatikan adalah keterlambatan perkembangan, nafsu makan menurun, serta riwayat ibu saat hamil seperti anemia atau kekurangan energi kronis.',
                    'Tanda-tanda tersebut tidak selalu berarti anak pasti stunting. Namun, jika muncul bersamaan dengan hasil pengukuran yang kurang baik, pemeriksaan lanjutan di puskesmas sangat disarankan.',
                ],
            ],
        ];
    }
}
