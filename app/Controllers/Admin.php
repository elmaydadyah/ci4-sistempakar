<?php

namespace App\Controllers;

use App\Models\AnakStatusGiziModel;
use App\Models\AdminModel;
use App\Models\AnakModel;
use App\Models\HasilDiagnosaModel;
use App\Libraries\RoleAccess;

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
        $roleAccess = new RoleAccess();
        $data['tb_users'] = $model->getUsers();
        $data['roleOptions'] = $roleAccess->roles();
        $data['permissionRows'] = $roleAccess->menus();
        $data['permissionActions'] = $roleAccess->actions();
        $data['supportedPermissionActions'] = $roleAccess->supportedActions();
        $data['rolePermissionMatrix'] = $roleAccess->permissionMatrix();

        return view('admin/users/index_users', $data);
    }

    public function settings()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $user = (new \App\Models\UsersModel())->find($userId);

        if (!$user) {
            return redirect()->to('/dashboard')->with('error', 'Data admin tidak ditemukan.');
        }

        return view('admin/settings/index_settings', [
            'user' => $user,
        ]);
    }

    public function updateSettings()
    {
        $userId = (int) session()->get('user_id');

        if ($userId <= 0) {
            return redirect()->to('/login');
        }

        $model = new \App\Models\UsersModel();
        $user = $model->find($userId);

        if (!$user) {
            return redirect()->to('/dashboard')->with('error', 'Data admin tidak ditemukan.');
        }

        $nama = trim((string) $this->request->getPost('nama'));
        $username = $this->normalizeUsername((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));

        if ($nama === '' || $username === '') {
            return redirect()->to('/adminsettings')->with('error', 'Nama dan username wajib diisi.');
        }

        if (!$this->isValidUsername($username)) {
            return redirect()->to('/adminsettings')->with('error', 'Username minimal 3 karakter dan hanya boleh huruf, angka, titik, strip, atau underscore.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/adminsettings')->with('error', 'Format email tidak valid.');
        }

        $usernameExists = $model
            ->where('username', $username)
            ->where('id_users !=', $userId)
            ->first();

        if ($usernameExists) {
            return redirect()->to('/adminsettings')->with('error', 'Username sudah digunakan admin lain.');
        }

        $emailExists = $model
            ->where('email', $email)
            ->where('id_users !=', $userId)
            ->first();

        if ($email !== '' && $emailExists) {
            return redirect()->to('/adminsettings')->with('error', 'Email sudah digunakan admin lain.');
        }

        $payload = [
            'nama' => $nama,
            'username' => $username,
            'email' => $email,
        ];

        $password = trim((string) $this->request->getPost('password'));
        if ($password !== '') {
            $payload['password'] = $password;
        }

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

            if (!in_array($foto->getMimeType(), $allowedTypes, true)) {
                return redirect()->to('/adminsettings')->with('error', 'Format foto harus JPG, JPEG, atau PNG.');
            }

            $uploadPath = FCPATH . 'uploads/foto_users';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $namaFoto = $foto->getRandomName();
            $foto->move($uploadPath, $namaFoto);
            $payload['foto'] = $namaFoto;

            if (!empty($user['foto']) && is_file($uploadPath . DIRECTORY_SEPARATOR . $user['foto'])) {
                unlink($uploadPath . DIRECTORY_SEPARATOR . $user['foto']);
            }
        }

        $model->update($userId, $payload);
        session()->set('nama', $nama);
        session()->set('username', $username);
        session()->set('email', $email);

        return redirect()->to('/adminsettings')->with('success', 'Profil admin berhasil diperbarui.');
    }

    public function indexAnak()
    {
        $anakId = (int) ($this->request->getGet('anak') ?? 0);
        $kelurahan = trim((string) $this->request->getGet('kelurahan'));
        $tanggalMulai = trim((string) $this->request->getGet('tanggal_mulai'));
        $tanggalSelesai = trim((string) $this->request->getGet('tanggal_selesai'));

        $builder = (new AnakModel())->orderBy('id_anak', 'DESC');

        if ($anakId > 0) {
            $builder->where('id_anak', $anakId);
        }

        if ($kelurahan !== '') {
            $builder->where('kelurahan', $kelurahan);
        }

        if ($tanggalMulai !== '') {
            $builder->where('created_at >=', $tanggalMulai . ' 00:00:00');
        }

        if ($tanggalSelesai !== '') {
            $builder->where('created_at <=', $tanggalSelesai . ' 23:59:59');
        }

        $data = [
            'tb_anak' => $builder->findAll(200),
            'kelurahan_options' => $this->getKelurahanOptions(),
            'filter' => [
                'anak' => $anakId,
                'kelurahan' => $kelurahan,
                'tanggal_mulai' => $tanggalMulai,
                'tanggal_selesai' => $tanggalSelesai,
            ],
        ];

        return view('admin/anak/index_anak', $data);
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

    public function updateAnak($id)
    {
        $model = new AnakModel();
        $anak = $model->find((int) $id);

        if (!$anak) {
            return redirect()->to('/adminanak')->with('error', 'Data anak tidak ditemukan.');
        }

        $namaAnak = trim((string) $this->request->getPost('nama_anak'));
        $jenisKelamin = trim((string) $this->request->getPost('jenis_kelamin'));
        $umurBulan = trim((string) $this->request->getPost('umur_bulan'));

        if ($namaAnak === '' || !in_array($jenisKelamin, ['L', 'P'], true) || $umurBulan === '' || !ctype_digit($umurBulan)) {
            return redirect()->to('/adminanak')->with('error', 'Nama anak, jenis kelamin, dan umur wajib diisi dengan benar.');
        }

        $payload = [
            'nama_anak' => $namaAnak,
            'nik' => trim((string) $this->request->getPost('nik')) ?: null,
            'jenis_kelamin' => $jenisKelamin,
            'jk_anak' => $jenisKelamin,
            'tanggal_lahir' => trim((string) $this->request->getPost('tanggal_lahir')) ?: null,
            'umur_bulan' => (int) $umurBulan,
            'umur_anak' => $umurBulan,
            'berat_badan' => $this->toDecimal($this->request->getPost('berat_badan')),
            'berat_anak' => $this->toDecimal($this->request->getPost('berat_badan')),
            'tinggi_badan' => $this->toDecimal($this->request->getPost('tinggi_badan')),
            'tinggi_anak' => $this->toDecimal($this->request->getPost('tinggi_badan')),
            'lingkar_lengan' => $this->toDecimal($this->request->getPost('lingkar_lengan')),
            'lingkar_kepala' => $this->toDecimal($this->request->getPost('lingkar_kepala')),
            'nama_ortu' => trim((string) $this->request->getPost('nama_ortu')) ?: null,
            'alamat' => trim((string) $this->request->getPost('alamat')) ?: null,
            'rt' => trim((string) $this->request->getPost('rt')) ?: null,
            'rw' => trim((string) $this->request->getPost('rw')) ?: null,
            'desa' => trim((string) $this->request->getPost('kelurahan')) ?: null,
            'kelurahan' => trim((string) $this->request->getPost('kelurahan')) ?: null,
            'kecamatan' => trim((string) $this->request->getPost('kecamatan')) ?: null,
            'riwayat_kehamilan' => trim((string) $this->request->getPost('riwayat_kehamilan')) ?: null,
            'pola_makan' => trim((string) $this->request->getPost('pola_makan')) ?: null,
            'tempat_tinggal' => trim((string) $this->request->getPost('tempat_tinggal')) ?: null,
        ];

        $model->update((int) $id, $payload);

        return redirect()->to('/adminanak')->with('success', 'Data anak berhasil diupdate.');
    }

    public function deleteAnak($id)
    {
        $model = new AnakModel();
        $anak = $model->find((int) $id);

        if (!$anak) {
            return redirect()->to('/adminanak')->with('error', 'Data anak tidak ditemukan.');
        }

        $db = db_connect();
        $anakId = (int) $id;
        $result = false;

        $db->transBegin();

        try {
            if ($db->tableExists('tb_hasil_diagnosa')) {
                $db->table('tb_hasil_diagnosa')
                    ->where('id_anak', $anakId)
                    ->delete();
            }

            if (!$model->delete($anakId)) {
                throw new \RuntimeException('Gagal menghapus data anak.');
            }

            $result = $db->transStatus();
        } catch (\Throwable $e) {
            $result = false;
        }

        if ($result) {
            $db->transCommit();
        } else {
            $db->transRollback();
        }

        return redirect()->to('/adminanak')->with(
            $result ? 'success' : 'error',
            $result ? 'Data anak dan hasil diagnosa terkait berhasil dihapus.' : 'Data anak gagal dihapus.'
        );
    }

    public function indexKasusGejala()
    {
        $model = new AdminModel();
        $data['tb_kasusgejala'] = $model->getKonsDetail();
        return view('admin/kasusgejala/index_kasusgejala', $data);
    }

    public function indexHasilDiagnosa()
    {
        $kelasHasil = strtoupper(trim((string) $this->request->getGet('kelas_hasil')));
        if (!in_array($kelasHasil, ['H1', 'H2', 'H3'], true)) {
            $kelasHasil = '';
        }

        $model = (new HasilDiagnosaModel())->orderBy('id_hasil_diagnosa', 'DESC');
        if ($kelasHasil !== '') {
            $model->where('kelas_hasil', $kelasHasil);
        }

        $data = [
            'tb_hasil_diagnosa' => $model->findAll(100),
            'kelas_hasil_options' => [
                'H1' => 'H1 - Risiko Stunting Tinggi',
                'H2' => 'H2 - Risiko Stunting Sedang',
                'H3' => 'H3 - Risiko Stunting Rendah',
            ],
            'filter' => [
                'kelas_hasil' => $kelasHasil,
            ],
        ];

        return view('admin/hasildiagnosa/index_hasil_diagnosa', $data);
    }

    public function deleteHasilDiagnosa($id)
    {
        $model = new HasilDiagnosaModel();
        $hasil = $model->find((int) $id);

        if (!$hasil) {
            return redirect()->to('/adminhasildiagnosa')->with('error', 'Data hasil diagnosa tidak ditemukan.');
        }

        $db = db_connect();
        $anakId = (int) ($hasil['id_anak'] ?? 0);

        $db->transBegin();
        $result = false;

        try {
            $result = $model->delete((int) $id);

            if (!$result) {
                throw new \RuntimeException('Gagal menghapus hasil diagnosa.');
            }

            if ($anakId > 0 && $db->tableExists('tb_anak')) {
                $masihDipakaiHasilLain = $db->table('tb_hasil_diagnosa')
                    ->where('id_anak', $anakId)
                    ->countAllResults() > 0;

                if (!$masihDipakaiHasilLain && !(new AnakModel())->delete($anakId)) {
                    throw new \RuntimeException('Gagal menghapus data anak terkait.');
                }
            }

            $result = $db->transStatus();
        } catch (\Throwable $e) {
            $result = false;
        }

        if ($result) {
            $db->transCommit();
        } else {
            $db->transRollback();
        }

        return redirect()->to('/adminhasildiagnosa')->with(
            $result ? 'success' : 'error',
            $result ? 'Data hasil diagnosa dan data anak terkait berhasil dihapus.' : 'Gagal menghapus data hasil diagnosa.'
        );
    }

    public function indexStandarAntropometri()
    {
        $db = db_connect();
        $indikatorOptions = ['TB/U', 'BB/U', 'BB/TB'];
        $indikatorAktif = (string) $this->request->getGet('indikator');
        if (!in_array($indikatorAktif, $indikatorOptions, true)) {
            $indikatorAktif = 'TB/U';
        }
        $jenisKelaminOptions = [
            '' => 'Semua',
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
        ];
        $jenisKelaminAktif = (string) $this->request->getGet('jenis_kelamin');
        if (!array_key_exists($jenisKelaminAktif, $jenisKelaminOptions)) {
            $jenisKelaminAktif = '';
        }
        $totalRows = 0;
        $standarRows = [];

        if ($db->tableExists('tb_standar_antropometri')) {
            $totalBuilder = $db->table('tb_standar_antropometri')
                ->where('indikator', $indikatorAktif);
            $dataBuilder = $db->table('tb_standar_antropometri')
                ->where('indikator', $indikatorAktif);

            if ($jenisKelaminAktif !== '') {
                $totalBuilder->where('jenis_kelamin', $jenisKelaminAktif);
                $dataBuilder->where('jenis_kelamin', $jenisKelaminAktif);
            }

            $totalRows = $totalBuilder->countAllResults();
            $standarRows = $dataBuilder
                ->orderBy('jenis_kelamin', 'ASC')
                ->orderBy('umur_bulan', 'ASC')
                ->orderBy('tinggi_cm', 'ASC')
                ->get()
                ->getResultArray();
        }

        $data = [
            'indikator_options' => $indikatorOptions,
            'indikator_aktif' => $indikatorAktif,
            'jenis_kelamin_options' => $jenisKelaminOptions,
            'jenis_kelamin_aktif' => $jenisKelaminAktif,
            'total_rows' => $totalRows,
            'tb_standar' => $standarRows,
        ];

        return view('admin/referensi/index_standar', $data);
    }

    public function updateStandarAntropometri($id)
    {
        $db = db_connect();
        if (!$db->tableExists('tb_standar_antropometri')) {
            return redirect()->to($this->getSafeAdminRedirect('/adminstandar'))->with('error', 'Tabel standar antropometri belum tersedia.');
        }

        $median = (float) $this->request->getPost('median');
        $sd = (float) $this->request->getPost('sd');

        if ($sd <= 0) {
            return redirect()->to($this->getSafeAdminRedirect('/adminstandar'))->with('error', 'SD harus lebih dari 0.');
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

        return redirect()->to($this->getSafeAdminRedirect('/adminstandar'))->with('success', 'Standar antropometri berhasil diupdate.');
    }

    public function indexRuleBased()
    {
        $db = db_connect();
        $rules = [];
        $nextRuleCode = 'RB001';

        if ($db->tableExists('tb_rule_based')) {
            $rules = $db->table('tb_rule_based rb')
                ->select('rb.*, g.nama_gejala, h.risiko_stunting')
                ->join('tb_gejala g', 'g.kode_gejala COLLATE utf8mb4_general_ci = rb.kode_gejala', 'left', false)
                ->join('tb_hipotesis h', 'h.kode_hipotesis = rb.kode_hipotesis', 'left')
                ->orderBy('rb.urutan', 'ASC')
                ->orderBy('rb.id_rule', 'ASC')
                ->get()
                ->getResultArray();

            $lastRule = $db->table('tb_rule_based')
                ->select('kode_rule')
                ->like('kode_rule', 'RB', 'after')
                ->orderBy('id_rule', 'DESC')
                ->get(1)
                ->getRowArray();

            if (!empty($lastRule['kode_rule']) && preg_match('/RB(\d+)/', (string) $lastRule['kode_rule'], $matches)) {
                $nextRuleCode = 'RB' . str_pad((string) ((int) $matches[1] + 1), 3, '0', STR_PAD_LEFT);
            }
        }

        return view('admin/referensi/index_rule_based', [
            'tb_rule_based' => $rules,
            'tb_gejala' => (new AdminModel())->getGejala(),
            'tb_hipotesis' => $db->tableExists('tb_hipotesis')
                ? $db->table('tb_hipotesis')->orderBy('kode_hipotesis', 'ASC')->get()->getResultArray()
                : [],
            'next_rule_code' => $nextRuleCode,
        ]);
    }

    public function createRuleBased()
    {
        $db = db_connect();
        if (!$db->tableExists('tb_rule_based')) {
            return redirect()->to('/adminrulebased')->with('error', 'Tabel rule based belum tersedia. Jalankan migration terlebih dahulu.');
        }

        [$payload, $error] = $this->getRuleBasedPostData();
        if ($error !== null) {
            return redirect()->to('/adminrulebased')->with('error', $error);
        }

        $exists = $db->table('tb_rule_based')
            ->where('kode_rule', $payload['kode_rule'])
            ->countAllResults();

        if ($exists > 0) {
            return redirect()->to('/adminrulebased')->with('error', 'Kode rule sudah digunakan.');
        }

        $relationExists = $db->table('tb_rule_based')
            ->where('kode_hipotesis', $payload['kode_hipotesis'])
            ->where('kode_gejala', $payload['kode_gejala'])
            ->countAllResults();

        if ($relationExists > 0) {
            return redirect()->to('/adminrulebased')->with('error', 'Relasi hipotesis dan gejala tersebut sudah ada.');
        }

        $payload['created_at'] = date('Y-m-d H:i:s');
        $payload['updated_at'] = date('Y-m-d H:i:s');
        $db->table('tb_rule_based')->insert($payload);

        return redirect()->to('/adminrulebased')->with('success', 'Rule based berhasil ditambahkan.');
    }

    public function updateRuleBased($id)
    {
        $db = db_connect();
        if (!$db->tableExists('tb_rule_based')) {
            return redirect()->to('/adminrulebased')->with('error', 'Tabel rule based belum tersedia.');
        }

        [$payload, $error] = $this->getRuleBasedPostData();
        if ($error !== null) {
            return redirect()->to('/adminrulebased')->with('error', $error);
        }

        $exists = $db->table('tb_rule_based')
            ->where('kode_rule', $payload['kode_rule'])
            ->where('id_rule !=', (int) $id)
            ->countAllResults();

        if ($exists > 0) {
            return redirect()->to('/adminrulebased')->with('error', 'Kode rule sudah digunakan oleh rule lain.');
        }

        $relationExists = $db->table('tb_rule_based')
            ->where('kode_hipotesis', $payload['kode_hipotesis'])
            ->where('kode_gejala', $payload['kode_gejala'])
            ->where('id_rule !=', (int) $id)
            ->countAllResults();

        if ($relationExists > 0) {
            return redirect()->to('/adminrulebased')->with('error', 'Relasi hipotesis dan gejala tersebut sudah ada di rule lain.');
        }

        $payload['updated_at'] = date('Y-m-d H:i:s');
        $db->table('tb_rule_based')
            ->where('id_rule', (int) $id)
            ->update($payload);

        return redirect()->to('/adminrulebased')->with('success', 'Rule based berhasil diupdate.');
    }

    public function deleteRuleBased($id)
    {
        $db = db_connect();
        if (!$db->tableExists('tb_rule_based')) {
            return redirect()->to('/adminrulebased')->with('error', 'Tabel rule based belum tersedia.');
        }

        $db->table('tb_rule_based')->where('id_rule', (int) $id)->delete();

        return redirect()->to('/adminrulebased')->with('success', 'Rule based berhasil dihapus.');
    }

    private function getRuleBasedPostData(): array
    {
        $kodeRule = strtoupper(trim((string) $this->request->getPost('kode_rule')));
        $namaRule = trim((string) $this->request->getPost('nama_rule'));
        $kodeHipotesis = strtoupper(trim((string) $this->request->getPost('kode_hipotesis')));
        $kodeGejala = strtoupper(trim((string) $this->request->getPost('kode_gejala')));

        if ($kodeRule === '' || $kodeHipotesis === '' || $kodeGejala === '') {
            return [[], 'Kode rule, hipotesis, dan gejala wajib diisi.'];
        }

        $db = db_connect();
        if (!$db->tableExists('tb_hipotesis')) {
            return [[], 'Tabel hipotesis belum tersedia.'];
        }

        $hipotesisExists = $db->table('tb_hipotesis')
            ->where('kode_hipotesis', $kodeHipotesis)
            ->countAllResults();

        if ($hipotesisExists === 0) {
            return [[], 'Kode hipotesis tidak ditemukan.'];
        }

        if (!$db->tableExists('tb_gejala')) {
            return [[], 'Tabel gejala belum tersedia.'];
        }

        $gejalaExists = $db->table('tb_gejala')
            ->where('kode_gejala', $kodeGejala)
            ->countAllResults();

        if ($gejalaExists === 0) {
            return [[], 'Kode gejala tidak ditemukan di data gejala.'];
        }

        return [[
            'kode_rule' => $kodeRule,
            'nama_rule' => $namaRule !== '' ? $namaRule : $kodeHipotesis . ' -> ' . $kodeGejala,
            'kode_hipotesis' => $kodeHipotesis,
            'kode_gejala' => $kodeGejala,
            'aktif' => $this->request->getPost('aktif') ? 1 : 0,
            'urutan' => max(0, (int) $this->request->getPost('urutan')),
            'catatan' => trim((string) $this->request->getPost('catatan')) ?: null,
        ], null];
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
            return redirect()->to($this->getSafeAdminRedirect('/adminprior'))->with('error', 'Tabel prior belum tersedia.');
        }

        $probabilitas = (float) $this->request->getPost('probabilitas');
        if ($probabilitas <= 0 || $probabilitas > 1) {
            return redirect()->to($this->getSafeAdminRedirect('/adminprior'))->with('error', 'Probabilitas harus lebih dari 0 dan maksimal 1.');
        }

        $db->table('tb_naive_bayes_prior')
            ->where('id_prior', (int) $id)
            ->update([
                'label' => trim((string) $this->request->getPost('label')),
                'probabilitas' => $probabilitas,
                'rekomendasi' => trim((string) $this->request->getPost('rekomendasi')) ?: null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to($this->getSafeAdminRedirect('/adminprior'))->with('success', 'Prior Naive Bayes berhasil diupdate.');
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
            return redirect()->to($this->getSafeAdminRedirect('/adminlikelihood'))->with('error', 'Tabel probabilitas antropometri belum tersedia.');
        }

        $probabilitas = (float) $this->request->getPost('probabilitas');
        if ($probabilitas <= 0 || $probabilitas > 1) {
            return redirect()->to($this->getSafeAdminRedirect('/adminlikelihood'))->with('error', 'Probabilitas harus lebih dari 0 dan maksimal 1.');
        }

        $db->table('tb_naive_bayes_likelihood')
            ->where('id_likelihood', (int) $id)
            ->update([
                'probabilitas' => $probabilitas,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to($this->getSafeAdminRedirect('/adminlikelihood'))->with('success', 'Probabilitas antropometri berhasil diupdate.');
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

    private function getSafeAdminRedirect(string $fallback): string
    {
        $target = (string) $this->request->getPost('redirect_to');

        if ($target !== '' && str_starts_with($target, base_url())) {
            return $target;
        }

        return $fallback;
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

    public function createUser()
    {
        $model = new AdminModel();

        $nama = trim((string) $this->request->getPost('nama'));
        $username = $this->normalizeUsername((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));
        $password = trim((string) $this->request->getPost('password'));

        if ($nama === '' || $username === '' || $password === '') {
            return redirect()->to('/adminusers')->with('error', 'Nama, username, dan password wajib diisi.');
        }

        if (!$this->isValidUsername($username)) {
            return redirect()->to('/adminusers')->with('error', 'Username minimal 3 karakter dan hanya boleh huruf, angka, titik, strip, atau underscore.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/adminusers')->with('error', 'Format email tidak valid.');
        }

        if (db_connect()->table('tb_users')->where('username', $username)->countAllResults() > 0) {
            return redirect()->to('/adminusers')->with('error', 'Username sudah digunakan.');
        }

        if ($email !== '' && db_connect()->table('tb_users')->where('email', $email)->countAllResults() > 0) {
            return redirect()->to('/adminusers')->with('error', 'Email sudah digunakan.');
        }

        $role = $this->normalizeAdminRole((string) $this->request->getPost('role'));

        $data = [
            'nama' => $nama,
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $foto = $this->request->getFile('foto');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $allowedTypes = ['image/jpg', 'image/jpeg', 'image/png'];

            if (!in_array($foto->getMimeType(), $allowedTypes, true)) {
                return redirect()->to('/adminusers')->with('error', 'Format foto harus JPG, JPEG, atau PNG.');
            }

            $uploadPath = FCPATH . 'uploads/foto_users';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }

            $namaFoto = $foto->getRandomName();
            $foto->move($uploadPath, $namaFoto);
            $data['foto'] = $namaFoto;
        }

        $result = $model->createUser($data);

        return redirect()->to('/adminusers')->with(
            $result ? 'success' : 'error',
            $result ? 'Admin berhasil ditambahkan.' : 'Gagal menambahkan admin.'
        );
    }

    public function updateUser($id)
    {
        $model = new AdminModel();
        $user = $model->getUserById($id);
        $id = (int) $id;

        if (!$user) {
            return redirect()->to('/adminusers')->with('error', 'Data admin tidak ditemukan.');
        }

        $nama = trim((string) $this->request->getPost('nama'));
        $username = $this->normalizeUsername((string) $this->request->getPost('username'));
        $email = trim((string) $this->request->getPost('email'));

        if ($nama === '' || $username === '') {
            return redirect()->to('/adminusers')->with('error', 'Nama dan username wajib diisi.');
        }

        if (!$this->isValidUsername($username)) {
            return redirect()->to('/adminusers')->with('error', 'Username minimal 3 karakter dan hanya boleh huruf, angka, titik, strip, atau underscore.');
        }

        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->to('/adminusers')->with('error', 'Format email tidak valid.');
        }

        $db = db_connect();
        if ($db->table('tb_users')->where('username', $username)->where('id_users !=', $id)->countAllResults() > 0) {
            return redirect()->to('/adminusers')->with('error', 'Username sudah digunakan.');
        }

        if ($email !== '' && $db->table('tb_users')->where('email', $email)->where('id_users !=', $id)->countAllResults() > 0) {
            return redirect()->to('/adminusers')->with('error', 'Email sudah digunakan.');
        }

        $data = [
            'nama' => $nama,
            'username' => $username,
            'email' => $email,
            'role' => $this->normalizeAdminRole((string) $this->request->getPost('role')),
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

    private function normalizeAdminRole(string $role): string
    {
        return (new RoleAccess())->normalizeRole($role);
    }

    private function normalizeUsername(string $username): string
    {
        $username = strtolower(trim($username));
        $username = preg_replace('/\s+/', '.', $username) ?? '';
        $username = preg_replace('/[^a-z0-9._-]/', '', $username) ?? '';

        return trim($username, '._-');
    }

    private function isValidUsername(string $username): bool
    {
        return (bool) preg_match('/^[a-z0-9._-]{3,50}$/', $username);
    }

    public function createRole()
    {
        $roleAccess = new RoleAccess();
        $code = (string) $this->request->getPost('role_code');
        $name = (string) $this->request->getPost('role_name');

        if (!$roleAccess->createRole($code, $name)) {
            return redirect()->to('/adminusers')->with('error', 'Role gagal ditambahkan. Pastikan kode role unik dan nama role terisi.');
        }

        return redirect()->to('/adminusers')->with('success', 'Role baru berhasil ditambahkan.');
    }

    public function updateRoleAccess()
    {
        $roleAccess = new RoleAccess();
        $role = (string) $this->request->getPost('role');
        $permissions = $this->request->getPost('permissions');
        $permissions = is_array($permissions) ? $permissions : [];

        if (!$roleAccess->savePermissions($role, $permissions)) {
            return redirect()->to('/adminusers')->with('error', 'Mapping hak akses gagal disimpan.');
        }

        return redirect()->to('/adminusers')->with('success', 'Mapping hak akses berhasil disimpan.');
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
