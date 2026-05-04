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
}