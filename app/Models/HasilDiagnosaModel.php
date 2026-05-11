<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilDiagnosaModel extends Model
{
    protected $table = 'tb_hasil_diagnosa';
    protected $primaryKey = 'id_hasil_diagnosa';
    protected $allowedFields = [
        'nama',
        'umur',
        'id_kasus',
        'nama_kasus',
        'persentase',
        'jumlah_gejala',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
