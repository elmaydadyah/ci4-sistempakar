<?php

namespace App\Models;

use CodeIgniter\Model;

class AnakModel extends Model
{
    protected $table = 'tb_anak';
    protected $primaryKey = 'id_anak';
    protected $allowedFields = [
        'nama_anak',
        'jenis_kelamin',
        'tanggal_lahir',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'nama_ortu',
        'alamat',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
