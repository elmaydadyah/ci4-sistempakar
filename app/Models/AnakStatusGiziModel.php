<?php

namespace App\Models;

use CodeIgniter\Model;

class AnakStatusGiziModel extends Model
{
    protected $table = 'tb_anak_status_gizi';
    protected $primaryKey = 'id_status_gizi';
    protected $allowedFields = [
        'no_urut',
        'nik',
        'nama',
        'jk',
        'tgl_lahir',
        'bb_lahir',
        'tb_lahir',
        'nama_ortu',
        'prov',
        'kab_kota',
        'kec',
        'puskesmas',
        'desa_kel',
        'posyandu',
        'rt',
        'rw',
        'alamat',
        'total_pengukuran',
        'usia_saat_ukur',
        'tanggal_pengukuran',
        'berat',
        'tinggi',
        'cara_ukur',
        'lila',
        'bb_u',
        'zs_bb_u',
        'tb_u',
        'zs_tb_u',
        'bb_tb',
        'zs_bb_tb',
        'naik_berat_badan',
        'jml_vit_a',
        'kpsp',
        'kia',
        'kelas_ibu_balita',
        'mbg',
        'detail',
        'uploaded_file',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getSummary(): array
    {
        return [
            'total' => $this->countAllResults(),
            'gizi_kurang' => $this->whereIn('bb_tb', ['Gizi Kurang', 'Gizi Buruk'])->countAllResults(),
            'pendek' => $this->whereIn('tb_u', ['Pendek', 'Sangat Pendek'])->countAllResults(),
            'latest_upload' => $this->select('uploaded_file, created_at')
                ->where('uploaded_file IS NOT NULL', null, false)
                ->orderBy('created_at', 'DESC')
                ->first(),
        ];
    }
}
