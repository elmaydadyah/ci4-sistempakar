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
        $this->primaryKey = 'id_gejala';
        return $this->findAll();
    }

    public function getGejalaById($id)
    {
        $this->table = 'tb_gejala';
        $this->primaryKey = 'id_gejala';
        return $this->find($id);
    }

    public function createGejala($data)
    {
        $this->table = 'tb_gejala';
        $this->primaryKey = 'id_gejala';
        $this->allowedFields = ['nama_gejala'];
        return $this->insert($data);
    }

    public function updateGejala($id, $data)
    {
        $this->table = 'tb_gejala';
        $this->primaryKey = 'id_gejala';
        $this->allowedFields = ['nama_gejala'];
        return $this->update($id, $data);
    }

    public function deleteGejala($id)
    {
        $this->table = 'tb_gejala';
        $this->primaryKey = 'id_gejala';
        return $this->delete($id);
    }

    public function getPenyakit()
    {
        $this->table = 'tb_kasus';
        $this->primaryKey = 'id_kasus';
        return $this->findAll();
    }

    public function getPenyakitById($id)
    {
        $this->table = 'tb_kasus';
        $this->primaryKey = 'id_kasus';
        return $this->find($id);
    }

    public function createPenyakit($data)
    {
        $this->table = 'tb_kasus';
        $this->primaryKey = 'id_kasus';
        $this->allowedFields = ['nama_kasus', 'deskripsi', 'solusi'];
        return $this->insert($data);
    }

    public function updatePenyakit($id, $data)
    {
        $this->table = 'tb_kasus';
        $this->primaryKey = 'id_kasus';
        $this->allowedFields = ['nama_kasus', 'deskripsi', 'solusi'];
        return $this->update($id, $data);
    }

    public function deletePenyakit($id)
    {
        $this->table = 'tb_kasus';
        $this->primaryKey = 'id_kasus';
        return $this->delete($id);
    }

    // Method untuk tb_users
    public function getUsers()
    {
        $this->table = 'tb_users';
        $this->primaryKey = 'id_users';
        return $this->findAll();
    }

    public function getUserById($id)
    {
        $this->table = 'tb_users';
        $this->primaryKey = 'id_users';
        return $this->find($id);
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

    // Method untuk update user
    public function updateUser($id, $data)
    {
        $this->table = 'tb_users';
        $this->primaryKey = 'id_users';
        $this->allowedFields = ['nama', 'email', 'password', 'role', 'foto'];
        return $this->update($id, $data);
    }
}
