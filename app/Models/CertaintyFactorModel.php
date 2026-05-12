<?php

namespace App\Models;

use CodeIgniter\Model;

class CertaintyFactorModel extends Model
{
    protected $table = 'tb_certainty_factor';
    protected $primaryKey = 'id_cf';
    protected $allowedFields = [
        'id_gejala',
        'bobot_cf',
        'keterangan',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
