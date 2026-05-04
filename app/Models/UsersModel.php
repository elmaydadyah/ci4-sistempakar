<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table = 'tb_users';
    protected $primaryKey = 'id_users';
    protected $allowedFields = ['nama_user', 'email', 'username', 'password'];
}