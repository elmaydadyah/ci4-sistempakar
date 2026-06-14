<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilDiagnosaModel extends Model
{
    protected $table = 'tb_hasil_diagnosa';
    protected $primaryKey = 'id_hasil_diagnosa';
    protected $allowedFields = [
        'id_anak',
        'nama',
        'nik',
        'jenis_kelamin',
        'tanggal_lahir',
        'umur',
        'berat_badan',
        'tinggi_badan',
        'lingkar_lengan',
        'lingkar_kepala',
        'alamat',
        'rt',
        'rw',
        'desa',
        'kelurahan',
        'kecamatan',
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
        'kelas_hasil',
        'probabilitas_prior',
        'probabilitas_likelihood',
        'probabilitas_posterior',
        'rekomendasi',
        'id_kasus',
        'nama_kasus',
        'persentase',
        'jumlah_gejala',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
