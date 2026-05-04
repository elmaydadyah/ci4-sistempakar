<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    // Method untuk tb_kons_detail
    public function getKonsDetail()
    {
        $this->table = 'tb_kons_detail';
        return $this->findAll();
    }

    // Method untuk tb_gejala
    public function getGejala()
    {
        $this->table = 'tb_gejala';
        return $this->findAll();
    }

    // Method untuk tb_users
    public function getUsers()
    {
        $this->table = 'tb_users';
        $this->primaryKey = 'id_users';
        return $this->findAll();
    }

    // Method untuk tb_konsultasi (menggunakan tb_kons_detail jika sama)
    public function getKonsultasi()
    {
        $this->table = 'tb_kons_detail'; // Sesuaikan jika tabel berbeda
        return $this->findAll();
    }

    // Method untuk delete user
    public function deleteUser($id)
    {
        $this->table = 'tb_users';
        $this->primaryKey = 'id_users';
        return $this->delete($id);
    }
}