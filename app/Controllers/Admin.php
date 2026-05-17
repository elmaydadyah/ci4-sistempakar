<?php

namespace App\Controllers;

use App\Models\AnakStatusGiziModel;
use App\Models\AdminModel;
use App\Models\CertaintyFactorModel;
use App\Models\AnakModel;
use App\Models\HasilDiagnosaModel;

class Admin extends BaseController
{
    public function indexGejala()
    {
        $model = new AdminModel();
        $data['tb_gejala'] = $model->getGejala();
        return view('admin/gejala/index_gejala', $data);
    }

    public function createGejala()
    {
        $kodeGejala = strtoupper(trim((string) $this->request->getPost('kode_gejala')));
        $namaGejala = trim((string) $this->request->getPost('nama_gejala'));

        if ($kodeGejala === '' || $namaGejala === '') {
            session()->setFlashdata('error', 'Kode dan nama gejala wajib diisi');
            return redirect()->to('/admingejala');
        }

        $model = new AdminModel();
        $result = $model->createGejala([
            'kode_gejala' => $kodeGejala,
            'nama_gejala' => $namaGejala,
        ]);

        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data gejala berhasil ditambahkan' : 'Gagal menambahkan data gejala');

        return redirect()->to('/admingejala');
    }

    public function updateGejala($id)
    {
        $kodeGejala = strtoupper(trim((string) $this->request->getPost('kode_gejala')));
        $namaGejala = trim((string) $this->request->getPost('nama_gejala'));

        if ($kodeGejala === '' || $namaGejala === '') {
            session()->setFlashdata('error', 'Kode dan nama gejala wajib diisi');
            return redirect()->to('/admingejala');
        }

        $model = new AdminModel();
        $result = $model->updateGejala($id, [
            'kode_gejala' => $kodeGejala,
            'nama_gejala' => $namaGejala,
        ]);

        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data gejala berhasil diupdate' : 'Gagal mengupdate data gejala');

        return redirect()->to('/admingejala');
    }

    public function deleteGejala($id)
    {
        $model = new AdminModel();
        $result = $model->deleteGejala($id);

        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data gejala berhasil dihapus' : 'Gagal menghapus data gejala');

        return redirect()->to('/admingejala');
    }

    public function indexHipotesis()
    {
        $db = db_connect();
        $data['tb_hipotesis'] = $db->tableExists('tb_hipotesis')
            ? (new AdminModel())->getHipotesis()
            : [];

        return view('admin/hipotesis/index_hipotesis', $data);
    }

    public function indexPenyakit()
    {
        $model = new AdminModel();
        $data['tb_penyakit'] = $model->getPenyakit();
        return view('admin/penyakit/index_penyakit', $data);
    }

    public function createPenyakit()
    {
        $data = $this->getPenyakitPostData();

        if ($data['nama_kasus'] === '') {
            session()->setFlashdata('error', 'Nama penyakit wajib diisi');
            return redirect()->to('/adminpenyakit');
        }

        $model = new AdminModel();
        $result = $model->createPenyakit($data);

        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data penyakit berhasil ditambahkan' : 'Gagal menambahkan data penyakit');

        return redirect()->to('/adminpenyakit');
    }

    public function updatePenyakit($id)
    {
        $data = $this->getPenyakitPostData();

        if ($data['nama_kasus'] === '') {
            session()->setFlashdata('error', 'Nama penyakit wajib diisi');
            return redirect()->to('/adminpenyakit');
        }

        $model = new AdminModel();
        $result = $model->updatePenyakit($id, $data);

        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data penyakit berhasil diupdate' : 'Gagal mengupdate data penyakit');

        return redirect()->to('/adminpenyakit');
    }

    public function deletePenyakit($id)
    {
        $model = new AdminModel();
        $result = $model->deletePenyakit($id);

        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data penyakit berhasil dihapus' : 'Gagal menghapus data penyakit');

        return redirect()->to('/adminpenyakit');
    }

    private function getPenyakitPostData(): array
    {
        return [
            'nama_kasus' => trim((string) $this->request->getPost('nama_kasus')),
            'deskripsi' => trim((string) $this->request->getPost('deskripsi')),
            'solusi' => trim((string) $this->request->getPost('solusi')),
        ];
    }

    public function indexUsers()
    {
        $model = new AdminModel();
        $data['tb_users'] = $model->getUsers();
        return view('admin/users/index_users', $data);
    }

    public function indexAnak()
    {
        $data = [
            'tb_anak' => (new AnakModel())
                ->orderBy('id_anak', 'DESC')
                ->findAll(100),
        ];

        return view('admin/anak/index_anak', $data);
    }

    public function indexKasusGejala()
    {
        $model = new AdminModel();
        $data['tb_kasusgejala'] = $model->getKonsDetail();
        return view('admin/kasusgejala/index_kasusgejala', $data);
    }

    public function indexKonsultasi()
    {
        $db = db_connect();
        $data = [
            'total_anak' => $db->tableExists('tb_anak') ? $db->table('tb_anak')->countAllResults() : 0,
            'total_data_latih' => $db->tableExists('tb_anak_status_gizi') ? $db->table('tb_anak_status_gizi')->countAllResults() : 0,
            'total_h1' => $db->tableExists('tb_hasil_diagnosa') && $db->fieldExists('kelas_hasil', 'tb_hasil_diagnosa') ? $db->table('tb_hasil_diagnosa')->where('kelas_hasil', 'H1')->countAllResults() : 0,
            'total_h2_h3' => $db->tableExists('tb_hasil_diagnosa') && $db->fieldExists('kelas_hasil', 'tb_hasil_diagnosa') ? $db->table('tb_hasil_diagnosa')->whereIn('kelas_hasil', ['H2', 'H3'])->countAllResults() : 0,
            'total_hasil' => $db->tableExists('tb_hasil_diagnosa') ? $db->table('tb_hasil_diagnosa')->countAllResults() : 0,
            'recent_anak' => $db->tableExists('tb_anak')
                ? $db->table('tb_anak')->orderBy('id_anak', 'DESC')->get(5)->getResultArray()
                : [],
            'recent_hasil' => $db->tableExists('tb_hasil_diagnosa')
                ? $db->table('tb_hasil_diagnosa')->orderBy('id_hasil_diagnosa', 'DESC')->get(5)->getResultArray()
                : [],
        ];

        return view('admin/konsultasi/index_konsultasi', $data);
    }

    public function indexHasilDiagnosa()
    {
        $data = [
            'tb_hasil_diagnosa' => (new HasilDiagnosaModel())
                ->orderBy('id_hasil_diagnosa', 'DESC')
                ->findAll(100),
        ];

        return view('admin/hasildiagnosa/index_hasil_diagnosa', $data);
    }

    public function indexCertaintyFactor()
    {
        $db = db_connect();
        $data = [
            'tb_gejala' => (new AdminModel())->getGejala(),
            'tb_cf' => $db->tableExists('tb_certainty_factor')
                ? $db->table('tb_certainty_factor cf')
                ->select('cf.*, g.nama_gejala')
                ->join('tb_gejala g', 'g.id_gejala = cf.id_gejala', 'left')
                ->orderBy('cf.id_cf', 'DESC')
                ->get()
                ->getResultArray()
                : [],
        ];

        return view('admin/certaintyfactor/index_cf', $data);
    }

    public function indexStandarAntropometri()
    {
        $db = db_connect();
        $indikatorOptions = ['TB/U', 'BB/U', 'BB/TB'];
        $indikatorAktif = (string) $this->request->getGet('indikator');
        if (!in_array($indikatorAktif, $indikatorOptions, true)) {
            $indikatorAktif = 'TB/U';
        }
        $perPage = 10;
        $page = max(1, (int) $this->request->getGet('page'));
        $totalRows = 0;
        $offset = ($page - 1) * $perPage;

        if ($db->tableExists('tb_standar_antropometri')) {
            $totalRows = $db->table('tb_standar_antropometri')
                ->where('indikator', $indikatorAktif)
                ->countAllResults();
        }

        $totalPages = max(1, (int) ceil($totalRows / $perPage));
        if ($page > $totalPages) {
            $page = $totalPages;
            $offset = ($page - 1) * $perPage;
        }

        $data = [
            'indikator_options' => $indikatorOptions,
            'indikator_aktif' => $indikatorAktif,
            'page' => $page,
            'per_page' => $perPage,
            'total_rows' => $totalRows,
            'total_pages' => $totalPages,
            'tb_standar' => $db->tableExists('tb_standar_antropometri')
                ? $db->table('tb_standar_antropometri')
                    ->where('indikator', $indikatorAktif)
                    ->orderBy('jenis_kelamin', 'ASC')
                    ->orderBy('umur_bulan', 'ASC')
                    ->orderBy('tinggi_cm', 'ASC')
                    ->get($perPage, $offset)
                    ->getResultArray()
                : [],
        ];

        return view('admin/referensi/index_standar', $data);
    }

    public function updateStandarAntropometri($id)
    {
        $db = db_connect();
        if (!$db->tableExists('tb_standar_antropometri')) {
            return redirect()->to('/adminstandar')->with('error', 'Tabel standar antropometri belum tersedia.');
        }

        $median = (float) $this->request->getPost('median');
        $sd = (float) $this->request->getPost('sd');

        if ($sd <= 0) {
            return redirect()->to('/adminstandar')->with('error', 'SD harus lebih dari 0.');
        }

        $db->table('tb_standar_antropometri')
            ->where('id_standar', (int) $id)
            ->update([
                'median' => $median,
                'sd' => $sd,
                'sumber' => trim((string) $this->request->getPost('sumber')) ?: null,
                'catatan' => trim((string) $this->request->getPost('catatan')) ?: null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/adminstandar')->with('success', 'Standar antropometri berhasil diupdate.');
    }

    public function indexNaiveBayesPrior()
    {
        $db = db_connect();
        $data = [
            'tb_prior' => $db->tableExists('tb_naive_bayes_prior')
                ? $db->table('tb_naive_bayes_prior')->orderBy('kelas', 'ASC')->get()->getResultArray()
                : [],
        ];

        return view('admin/referensi/index_prior', $data);
    }

    public function updateNaiveBayesPrior($id)
    {
        $db = db_connect();
        if (!$db->tableExists('tb_naive_bayes_prior')) {
            return redirect()->to('/adminprior')->with('error', 'Tabel prior belum tersedia.');
        }

        $probabilitas = (float) $this->request->getPost('probabilitas');
        if ($probabilitas <= 0 || $probabilitas > 1) {
            return redirect()->to('/adminprior')->with('error', 'Probabilitas harus lebih dari 0 dan maksimal 1.');
        }

        $db->table('tb_naive_bayes_prior')
            ->where('id_prior', (int) $id)
            ->update([
                'label' => trim((string) $this->request->getPost('label')),
                'probabilitas' => $probabilitas,
                'rekomendasi' => trim((string) $this->request->getPost('rekomendasi')) ?: null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/adminprior')->with('success', 'Prior Naive Bayes berhasil diupdate.');
    }

    public function indexNaiveBayesLikelihood()
    {
        $db = db_connect();
        $data = [
            'tb_likelihood' => $db->tableExists('tb_naive_bayes_likelihood')
                ? $db->table('tb_naive_bayes_likelihood')
                    ->orderBy('indikator', 'ASC')
                    ->orderBy('kategori', 'ASC')
                    ->orderBy('kelas', 'ASC')
                    ->get()
                    ->getResultArray()
                : [],
        ];

        return view('admin/referensi/index_likelihood', $data);
    }

    public function updateNaiveBayesLikelihood($id)
    {
        $db = db_connect();
        if (!$db->tableExists('tb_naive_bayes_likelihood')) {
            return redirect()->to('/adminlikelihood')->with('error', 'Tabel likelihood belum tersedia.');
        }

        $probabilitas = (float) $this->request->getPost('probabilitas');
        if ($probabilitas <= 0 || $probabilitas > 1) {
            return redirect()->to('/adminlikelihood')->with('error', 'Probabilitas harus lebih dari 0 dan maksimal 1.');
        }

        $db->table('tb_naive_bayes_likelihood')
            ->where('id_likelihood', (int) $id)
            ->update([
                'probabilitas' => $probabilitas,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/adminlikelihood')->with('success', 'Likelihood Naive Bayes berhasil diupdate.');
    }

    public function indexNilaiProbabilitas()
    {
        $db = db_connect();
        $rows = [];

        if ($db->tableExists('tb_nilai_probabilitas') && $db->tableExists('tb_gejala')) {
            $rows = $db->table('tb_nilai_probabilitas np')
                ->select('g.kode_gejala, g.nama_gejala, np.kode_hipotesis, np.nilai_probabilitas')
                ->join('tb_gejala g', 'g.id_gejala = np.id_gejala', 'left')
                ->orderBy('g.id_gejala', 'ASC')
                ->orderBy('np.kode_hipotesis', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data['tb_nilai_probabilitas'] = $this->formatNilaiProbabilitasRows($rows);

        return view('admin/referensi/index_nilai_probabilitas', $data);
    }

    private function formatNilaiProbabilitasRows(array $rows): array
    {
        $formatted = [];
        foreach ($rows as $row) {
            $kodeGejala = (string) ($row['kode_gejala'] ?? '');
            if ($kodeGejala === '') {
                continue;
            }

            if (!isset($formatted[$kodeGejala])) {
                $formatted[$kodeGejala] = [
                    'kode_gejala' => $kodeGejala,
                    'nama_gejala' => (string) ($row['nama_gejala'] ?? ''),
                    'H1' => null,
                    'H2' => null,
                    'H3' => null,
                ];
            }

            $kodeHipotesis = (string) ($row['kode_hipotesis'] ?? '');
            if (in_array($kodeHipotesis, ['H1', 'H2', 'H3'], true)) {
                $formatted[$kodeGejala][$kodeHipotesis] = $row['nilai_probabilitas'];
            }
        }

        return array_values($formatted);
    }

    public function createCertaintyFactor()
    {
        $data = $this->getCertaintyFactorPostData();

        if ($data['id_gejala'] <= 0 || $data['bobot_cf'] < 0 || $data['bobot_cf'] > 1) {
            session()->setFlashdata('error', 'Gejala wajib dipilih dan bobot CF harus berada di rentang 0 sampai 1.');
            return redirect()->to('/admincf');
        }

        $model = new CertaintyFactorModel();
        if ($model->where('id_gejala', $data['id_gejala'])->first()) {
            session()->setFlashdata('error', 'Gejala tersebut sudah memiliki bobot CF.');
            return redirect()->to('/admincf');
        }

        $result = $model->insert($data);
        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data CF berhasil ditambahkan' : 'Gagal menambahkan data CF');

        return redirect()->to('/admincf');
    }

    public function updateCertaintyFactor($id)
    {
        $data = $this->getCertaintyFactorPostData();

        if ($data['id_gejala'] <= 0 || $data['bobot_cf'] < 0 || $data['bobot_cf'] > 1) {
            session()->setFlashdata('error', 'Gejala wajib dipilih dan bobot CF harus berada di rentang 0 sampai 1.');
            return redirect()->to('/admincf');
        }

        $model = new CertaintyFactorModel();
        $existing = $model->where('id_gejala', $data['id_gejala'])
            ->where('id_cf !=', $id)
            ->first();

        if ($existing) {
            session()->setFlashdata('error', 'Gejala tersebut sudah memiliki bobot CF.');
            return redirect()->to('/admincf');
        }

        $result = $model->update($id, $data);
        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data CF berhasil diupdate' : 'Gagal mengupdate data CF');

        return redirect()->to('/admincf');
    }

    public function deleteCertaintyFactor($id)
    {
        $result = (new CertaintyFactorModel())->delete($id);
        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data CF berhasil dihapus' : 'Gagal menghapus data CF');

        return redirect()->to('/admincf');
    }

    public function indexStatusGizi()
    {
        $model = new AnakStatusGiziModel();

        $data = [
            'tb_anak_status_gizi' => $model->orderBy('id_status_gizi', 'DESC')->findAll(100),
            'summary' => $model->getSummary(),
        ];

        return view('admin/statusgizi/index_statusgizi', $data);
    }

    private function getCertaintyFactorPostData(): array
    {
        return [
            'id_gejala' => (int) $this->request->getPost('id_gejala'),
            'bobot_cf' => (float) $this->request->getPost('bobot_cf'),
            'keterangan' => trim((string) $this->request->getPost('keterangan')) ?: null,
        ];
    }

    public function uploadStatusGizi()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'File Excel wajib dipilih.');
            return redirect()->to('/adminstatusgizi');
        }

        $extension = strtolower($file->getClientExtension());
        if (!in_array($extension, ['xls', 'html', 'htm'], true)) {
            session()->setFlashdata('error', 'Format file harus .xls hasil export Excel.');
            return redirect()->to('/adminstatusgizi');
        }

        $html = file_get_contents($file->getTempName());
        if ($html === false || trim($html) === '') {
            session()->setFlashdata('error', 'File tidak bisa dibaca atau kosong.');
            return redirect()->to('/adminstatusgizi');
        }

        $rows = $this->parseStatusGiziRows($html, $file->getClientName());
        if ($rows === []) {
            session()->setFlashdata('error', 'Tidak ada data anak yang bisa diimport dari file tersebut.');
            return redirect()->to('/adminstatusgizi');
        }

        $model = new AnakStatusGiziModel();
        $inserted = 0;

        foreach (array_chunk($rows, 500) as $chunk) {
            if ($model->insertBatch($chunk)) {
                $inserted += count($chunk);
            }
        }

        $uploadPath = WRITEPATH . 'uploads/status_gizi';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        if (!$file->hasMoved()) {
            $file->move($uploadPath, $file->getRandomName());
        }

        session()->setFlashdata('success', $inserted . ' data status gizi anak berhasil diimport.');

        return redirect()->to('/adminstatusgizi');
    }

    public function deleteUser($id)
    {
        $model = new AdminModel();
        $result = $model->deleteUser($id);

        if ($result) {
            session()->setFlashdata('success', 'Data user berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data user');
        }

        return redirect()->to('/adminusers');
    }

    public function updateUser($id)
    {
        $model = new AdminModel();
        $user = $model->getUserById($id);

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role'),
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password;
        }

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

            if (!in_array($foto->getMimeType(), $allowedTypes, true)) {
                session()->setFlashdata('error', 'Format foto harus JPG, JPEG, atau PNG');
                return redirect()->to('/adminusers');
            }

            $uploadPath = FCPATH . 'uploads/foto_users';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $namaFoto = $foto->getRandomName();
            $foto->move($uploadPath, $namaFoto);
            $data['foto'] = $namaFoto;

            if (!empty($user['foto']) && is_file($uploadPath . DIRECTORY_SEPARATOR . $user['foto'])) {
                unlink($uploadPath . DIRECTORY_SEPARATOR . $user['foto']);
            }
        }

        $result = $model->updateUser($id, $data);

        if ($result) {
            session()->setFlashdata('success', 'Data user berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data user');
        }

        return redirect()->to('/adminusers');
    }

    private function parseStatusGiziRows(string $html, string $fileName): array
    {
        preg_match_all('/<tr[^>]*>(.*?)<\/tr>/is', $html, $matches);

        $rows = [];
        foreach ($matches[1] as $index => $rowHtml) {
            preg_match_all('/<t[dh][^>]*>(.*?)<\/t[dh]>/is', $rowHtml, $cellMatches);
            $cells = array_map(fn ($cell) => $this->cleanExcelCell($cell), $cellMatches[1] ?? []);

            if ($index === 0 || count($cells) < 30 || strtolower((string) ($cells[0] ?? '')) === 'no') {
                continue;
            }

            // Beberapa export file ini tidak membawa kolom "Total Pengukuran" pada baris data.
            if (count($cells) === 36) {
                array_splice($cells, 17, 0, [null]);
            }

            $rows[] = [
                'no_urut' => $this->toInt($cells[0] ?? null),
                'nik' => $this->nullableString($cells[1] ?? null),
                'nama' => $this->nullableString($cells[2] ?? null) ?? '-',
                'jk' => $this->nullableString($cells[3] ?? null),
                'tgl_lahir' => $this->toDate($cells[4] ?? null),
                'bb_lahir' => $this->toDecimal($cells[5] ?? null),
                'tb_lahir' => $this->toDecimal($cells[6] ?? null),
                'nama_ortu' => $this->nullableString($cells[7] ?? null),
                'prov' => $this->nullableString($cells[8] ?? null),
                'kab_kota' => $this->nullableString($cells[9] ?? null),
                'kec' => $this->nullableString($cells[10] ?? null),
                'puskesmas' => $this->nullableString($cells[11] ?? null),
                'desa_kel' => $this->nullableString($cells[12] ?? null),
                'posyandu' => $this->nullableString($cells[13] ?? null),
                'rt' => $this->nullableString($cells[14] ?? null),
                'rw' => $this->nullableString($cells[15] ?? null),
                'alamat' => $this->nullableString($cells[16] ?? null),
                'total_pengukuran' => $this->nullableString($cells[17] ?? null),
                'usia_saat_ukur' => $this->nullableString($cells[18] ?? null),
                'tanggal_pengukuran' => $this->toDate($cells[19] ?? null),
                'berat' => $this->toDecimal($cells[20] ?? null),
                'tinggi' => $this->toDecimal($cells[21] ?? null),
                'cara_ukur' => $this->nullableString($cells[22] ?? null),
                'lila' => $this->toDecimal($cells[23] ?? null),
                'bb_u' => $this->nullableString($cells[24] ?? null),
                'zs_bb_u' => $this->toDecimal($cells[25] ?? null),
                'tb_u' => $this->nullableString($cells[26] ?? null),
                'zs_tb_u' => $this->toDecimal($cells[27] ?? null),
                'bb_tb' => $this->nullableString($cells[28] ?? null),
                'zs_bb_tb' => $this->toDecimal($cells[29] ?? null),
                'naik_berat_badan' => $this->nullableString($cells[30] ?? null),
                'jml_vit_a' => $this->toInt($cells[31] ?? null),
                'kpsp' => $this->nullableString($cells[32] ?? null),
                'kia' => $this->nullableString($cells[33] ?? null),
                'kelas_ibu_balita' => $this->nullableString($cells[34] ?? null),
                'mbg' => $this->nullableString($cells[35] ?? null),
                'detail' => $this->nullableString($cells[36] ?? null),
                'uploaded_file' => $fileName,
            ];
        }

        return $rows;
    }

    private function cleanExcelCell(string $cell): ?string
    {
        $cell = preg_replace('/<br\s*\/?>/i', ' ', $cell);
        $cell = html_entity_decode(strip_tags((string) $cell), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $cell = str_replace("\xc2\xa0", ' ', $cell);
        $cell = preg_replace('/\s+/u', ' ', trim($cell));

        return $cell === '' ? null : $cell;
    }

    private function nullableString(?string $value): ?string
    {
        $value = $value === null ? null : trim($value);

        return $value === '' ? null : $value;
    }

    private function toDate(?string $value): ?string
    {
        if (!$value || !preg_match('/^\d{4}-\d{2}-\d{2}$/', trim($value))) {
            return null;
        }

        return trim($value);
    }

    private function toDecimal(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = str_replace(',', '.', trim($value));

        return is_numeric($value) ? (float) $value : null;
    }

    private function toInt(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        preg_match('/-?\d+/', $value, $matches);

        return isset($matches[0]) ? (int) $matches[0] : null;
    }
}
