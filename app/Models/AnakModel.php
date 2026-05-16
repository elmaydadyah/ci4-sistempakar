<?php

namespace App\Models;

use CodeIgniter\Model;

class AnakModel extends Model
{
    protected $table = 'tb_anak';
    protected $primaryKey = 'id_anak';
    protected $allowedFields = [
        'nama_anak',
        'nik',
        'jenis_kelamin',
        'tanggal_lahir',
        'umur_bulan',
        'berat_badan',
        'tinggi_badan',
        'lingkar_lengan',
        'lingkar_kepala',
        'nama_ortu',
        'alamat',
        'riwayat_kehamilan',
        'pola_makan',
        'tempat_tinggal',
        'zs_bb_u',
        'kategori_bb_u',
        'zs_tb_u',
        'kategori_tb_u',
        'zs_bb_tb',
        'kategori_bb_tb',
        'gejala_zscore',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
