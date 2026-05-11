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
            . view('layout/landing/contact')
            . view('layout/landing/faq')
            . view('layout/landing/footer');
    }

    public function konseling(): string
    {
        return view('layout/landing/header')
            . view('layout/landing/navbar')
            . '<main class="landing-shell">'
            . view('layout/landing/konseling')
            . view('layout/landing/footer');
    }

    public function storeAnak()
    {
        $rules = [
            'nama_anak' => 'required|min_length[2]|max_length[150]',
            'jenis_kelamin' => 'required|in_list[L,P]',
            'tanggal_lahir' => 'permit_empty|valid_date[Y-m-d]',
            'umur_bulan' => 'required|integer|greater_than_equal_to[0]|less_than_equal_to[60]',
            'berat_badan' => 'permit_empty|decimal',
            'tinggi_badan' => 'permit_empty|decimal',
            'nama_ortu' => 'permit_empty|max_length[150]',
            'alamat' => 'permit_empty',
        ];

        $old = [
            'nama_anak' => trim((string) $this->request->getPost('nama_anak')),
            'jenis_kelamin' => (string) $this->request->getPost('jenis_kelamin'),
            'tanggal_lahir' => (string) $this->request->getPost('tanggal_lahir'),
            'umur_bulan' => (string) $this->request->getPost('umur_bulan'),
            'berat_badan' => (string) $this->request->getPost('berat_badan'),
            'tinggi_badan' => (string) $this->request->getPost('tinggi_badan'),
            'nama_ortu' => trim((string) $this->request->getPost('nama_ortu')),
            'alamat' => trim((string) $this->request->getPost('alamat')),
        ];

        if (!$this->validate($rules)) {
            return redirect()->to('/konseling')
                ->with('konseling_errors', $this->validator->getErrors())
                ->with('konseling_old', $old);
        }

        $model = new AnakModel();
        $model->insert([
            'nama_anak' => $old['nama_anak'],
            'jenis_kelamin' => $old['jenis_kelamin'],
            'tanggal_lahir' => $old['tanggal_lahir'] !== '' ? $old['tanggal_lahir'] : null,
            'umur_bulan' => (int) $old['umur_bulan'],
            'berat_badan' => $old['berat_badan'] !== '' ? (float) $old['berat_badan'] : null,
            'tinggi_badan' => $old['tinggi_badan'] !== '' ? (float) $old['tinggi_badan'] : null,
            'nama_ortu' => $old['nama_ortu'] !== '' ? $old['nama_ortu'] : null,
            'alamat' => $old['alamat'] !== '' ? $old['alamat'] : null,
        ]);

        return redirect()->to('/konseling')
            ->with('konseling_success', 'Data anak berhasil disimpan. Silakan lanjut ke halaman diagnosa.');
    }
}
