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
            . view('layout/landing/features')
            . view('layout/landing/contact')
            . view('layout/landing/faq')
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
}
