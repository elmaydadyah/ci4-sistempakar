<?php

namespace App\Controllers;

use App\Models\AnakStatusGiziModel;
use App\Models\AdminModel;

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
        $namaGejala = trim((string) $this->request->getPost('nama_gejala'));

        if ($namaGejala === '') {
            session()->setFlashdata('error', 'Nama gejala wajib diisi');
            return redirect()->to('/admingejala');
        }

        $model = new AdminModel();
        $result = $model->createGejala([
            'nama_gejala' => $namaGejala,
        ]);

        session()->setFlashdata($result ? 'success' : 'error', $result ? 'Data gejala berhasil ditambahkan' : 'Gagal menambahkan data gejala');

        return redirect()->to('/admingejala');
    }

    public function updateGejala($id)
    {
        $namaGejala = trim((string) $this->request->getPost('nama_gejala'));

        if ($namaGejala === '') {
            session()->setFlashdata('error', 'Nama gejala wajib diisi');
            return redirect()->to('/admingejala');
        }

        $model = new AdminModel();
        $result = $model->updateGejala($id, [
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

    public function indexKasusGejala()
    {
        $model = new AdminModel();
        $data['tb_kons_detail'] = $model->getKonsDetail();
        return view('admin/kasusgejala/index_kasusgejala', $data);
    }

    public function indexKonsultasi()
    {
        $model = new AdminModel();
        $data['tb_kons_detail'] = $model->getKonsultasi(); // Jika tabel konsultasi berbeda, ganti method
        return view('admin/konsultasi/index_konsultasi', $data);
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
