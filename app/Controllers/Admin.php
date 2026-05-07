<?php

namespace App\Controllers;

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
}
