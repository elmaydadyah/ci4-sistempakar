<?php

namespace App\Controllers;

use App\Models\UsersModel;
use App\Libraries\RoleAccess;

class Auth extends BaseController
{
    public function login()
    {
        helper(['form']);

        if ($this->request->is('post')) {

            $username = $this->normalizeUsername((string) $this->request->getPost('username'));
            $password = trim($this->request->getPost('password'));

            $userModel = new UsersModel();
            $user = $userModel->where('username', $username)->first();

            // DEBUG (hapus kalau sudah normal)
            // dd($username, $password, $user);

            if ($user && $user['password'] === $password) {

                session()->regenerate();

                session()->set([
                    'isLoggedIn' => true,
                    'user_id' => $user['id_users'],
                    'username' => $user['username'] ?? '',
                    'email' => $user['email'],
                    'nama' => $user['nama'], // FIXED (bukan nama_user)
                    'role' => $this->normalizeRole((string) ($user['role'] ?? 'admin1')),
                ]);

                return redirect()->to('/dashboard')->with('login_success', 'Login berhasil. Mengalihkan ke dashboard.');

            } else {
                return redirect()->back()->with('error', 'Username atau password salah');
            }
        }

        return view('auth/login');
    }

    public function register()
    {
        if ($this->request->is('post')) {

            $userModel = new UsersModel();
            $nama = trim((string) $this->request->getPost('nama'));
            $username = $this->normalizeUsername((string) $this->request->getPost('username'));
            $email = trim((string) $this->request->getPost('email'));
            $password = trim((string) $this->request->getPost('password'));

            if ($nama === '' || $username === '' || $password === '') {
                return redirect()->back()->with('error', 'Nama, username, dan password wajib diisi.');
            }

            if (!preg_match('/^[a-z0-9._-]{3,50}$/', $username)) {
                return redirect()->back()->with('error', 'Username minimal 3 karakter dan hanya boleh huruf, angka, titik, strip, atau underscore.');
            }

            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()->with('error', 'Format email tidak valid.');
            }

            if ($userModel->where('username', $username)->first()) {
                return redirect()->back()->with('error', 'Username sudah digunakan.');
            }

            if ($email !== '' && $userModel->where('email', $email)->first()) {
                return redirect()->back()->with('error', 'Email sudah digunakan.');
            }

            $userModel->save([
                'nama' => $nama,
                'username' => $username,
                'email' => $email,
                'password' => $password, // belum hash
                'role' => 'admin1'
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

    private function normalizeRole(string $role): string
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
}
