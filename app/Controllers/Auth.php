<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Auth extends BaseController
{
    public function login()
    {
        helper(['form']);

        if ($this->request->is('post')) {

            $email = trim($this->request->getPost('email'));
            $password = trim($this->request->getPost('password'));

            $userModel = new UsersModel();
            $user = $userModel->where('email', $email)->first();

            // DEBUG (hapus kalau sudah normal)
            // dd($email, $password, $user);

            if ($user && $user['password'] === $password) {

                session()->regenerate();

                session()->set([
                    'isLoggedIn' => true,
                    'user_id' => $user['id_users'],
                    'email' => $user['email'],
                    'nama' => $user['nama'] // FIXED (bukan nama_user)
                ]);

                return redirect()->to('/dashboard');

            } else {
                return redirect()->back()->with('error', 'Email atau password salah');
            }
        }

        return view('auth/login');
    }

    public function register()
    {
        if ($this->request->is('post')) {

            $userModel = new UsersModel();

            $userModel->save([
                'nama' => $this->request->getPost('nama'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'), // belum hash
                'role' => 'admin'
            ]);

            return redirect()->to('/login')->with('success', 'Register berhasil');
        }

        return view('auth/register');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/');
    }
}
