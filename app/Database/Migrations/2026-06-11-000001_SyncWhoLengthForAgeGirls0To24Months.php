<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncWhoLengthForAgeGirls0To24Months extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('tb_standar_antropometri')) {
            return;
        }

        $now = date('Y-m-d H:i:s');

        foreach ($this->standards() as $row) {
            $payload = array_merge($row, [
                'indikator' => 'TB/U',
                'jenis_kelamin' => 'P',
                'umur_min_bulan' => null,
                'umur_max_bulan' => null,
                'tinggi_cm' => null,
                'sd' => round(((float) $row['sd_pos1'] - (float) $row['sd_neg1']) / 2, 2),
                'sumber' => 'Standar Panjang Badan menurut Umur (PB/U) Anak Perempuan Umur 0-24 Bulan',
                'catatan' => 'Data WHO PB/U anak perempuan 0-24 bulan dari file Excel rujukan skripsi.',
                'updated_at' => $now,
            ]);

            $existing = $this->db->table('tb_standar_antropometri')
                ->where('indikator', 'TB/U')
                ->where('jenis_kelamin', 'P')
                ->where('umur_bulan', $row['umur_bulan'])
                ->get()
                ->getRowArray();

            if ($existing) {
                $this->db->table('tb_standar_antropometri')
                    ->where('id_standar', $existing['id_standar'])
                    ->update($payload);

                continue;
            }

            $payload['created_at'] = $now;
            $this->db->table('tb_standar_antropometri')->insert($payload);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('tb_standar_antropometri')) {
            return;
        }

        $this->db->table('tb_standar_antropometri')
            ->where('indikator', 'TB/U')
            ->where('jenis_kelamin', 'P')
            ->where('umur_bulan >=', 0)
            ->where('umur_bulan <=', 24)
            ->delete();
    }

    private function standards(): array
    {
        return [
            ['umur_bulan' => 0, 'sd_neg3' => 43.6, 'sd_neg2' => 45.4, 'sd_neg1' => 47.3, 'median' => 49.1, 'sd_pos1' => 51.0, 'sd_pos2' => 52.9, 'sd_pos3' => 54.7],
            ['umur_bulan' => 1, 'sd_neg3' => 47.8, 'sd_neg2' => 49.8, 'sd_neg1' => 51.7, 'median' => 53.7, 'sd_pos1' => 55.6, 'sd_pos2' => 57.6, 'sd_pos3' => 59.5],
            ['umur_bulan' => 2, 'sd_neg3' => 51.0, 'sd_neg2' => 53.0, 'sd_neg1' => 55.0, 'median' => 57.1, 'sd_pos1' => 59.1, 'sd_pos2' => 61.1, 'sd_pos3' => 63.2],
            ['umur_bulan' => 3, 'sd_neg3' => 53.5, 'sd_neg2' => 55.6, 'sd_neg1' => 57.7, 'median' => 59.8, 'sd_pos1' => 61.9, 'sd_pos2' => 64.0, 'sd_pos3' => 66.1],
            ['umur_bulan' => 4, 'sd_neg3' => 55.6, 'sd_neg2' => 57.8, 'sd_neg1' => 59.9, 'median' => 62.1, 'sd_pos1' => 64.3, 'sd_pos2' => 66.4, 'sd_pos3' => 68.6],
            ['umur_bulan' => 5, 'sd_neg3' => 57.4, 'sd_neg2' => 59.6, 'sd_neg1' => 61.8, 'median' => 64.0, 'sd_pos1' => 66.2, 'sd_pos2' => 68.5, 'sd_pos3' => 70.7],
            ['umur_bulan' => 6, 'sd_neg3' => 58.9, 'sd_neg2' => 61.2, 'sd_neg1' => 63.5, 'median' => 65.7, 'sd_pos1' => 68.0, 'sd_pos2' => 70.3, 'sd_pos3' => 72.5],
            ['umur_bulan' => 7, 'sd_neg3' => 60.3, 'sd_neg2' => 62.7, 'sd_neg1' => 65.0, 'median' => 67.3, 'sd_pos1' => 69.6, 'sd_pos2' => 71.9, 'sd_pos3' => 74.2],
            ['umur_bulan' => 8, 'sd_neg3' => 61.7, 'sd_neg2' => 64.0, 'sd_neg1' => 66.4, 'median' => 68.7, 'sd_pos1' => 71.1, 'sd_pos2' => 73.5, 'sd_pos3' => 75.8],
            ['umur_bulan' => 9, 'sd_neg3' => 62.9, 'sd_neg2' => 65.3, 'sd_neg1' => 67.7, 'median' => 70.1, 'sd_pos1' => 72.6, 'sd_pos2' => 75.0, 'sd_pos3' => 77.4],
            ['umur_bulan' => 10, 'sd_neg3' => 64.1, 'sd_neg2' => 66.5, 'sd_neg1' => 69.0, 'median' => 71.5, 'sd_pos1' => 73.9, 'sd_pos2' => 76.4, 'sd_pos3' => 78.9],
            ['umur_bulan' => 11, 'sd_neg3' => 65.2, 'sd_neg2' => 67.7, 'sd_neg1' => 70.3, 'median' => 72.8, 'sd_pos1' => 75.3, 'sd_pos2' => 77.8, 'sd_pos3' => 80.3],
            ['umur_bulan' => 12, 'sd_neg3' => 66.3, 'sd_neg2' => 68.9, 'sd_neg1' => 71.4, 'median' => 74.0, 'sd_pos1' => 76.6, 'sd_pos2' => 79.2, 'sd_pos3' => 81.7],
            ['umur_bulan' => 13, 'sd_neg3' => 67.3, 'sd_neg2' => 70.0, 'sd_neg1' => 72.6, 'median' => 75.2, 'sd_pos1' => 77.8, 'sd_pos2' => 80.5, 'sd_pos3' => 83.1],
            ['umur_bulan' => 14, 'sd_neg3' => 68.3, 'sd_neg2' => 71.0, 'sd_neg1' => 73.7, 'median' => 76.4, 'sd_pos1' => 79.1, 'sd_pos2' => 81.7, 'sd_pos3' => 84.4],
            ['umur_bulan' => 15, 'sd_neg3' => 69.3, 'sd_neg2' => 72.0, 'sd_neg1' => 74.8, 'median' => 77.5, 'sd_pos1' => 80.2, 'sd_pos2' => 83.0, 'sd_pos3' => 85.7],
            ['umur_bulan' => 16, 'sd_neg3' => 70.2, 'sd_neg2' => 73.0, 'sd_neg1' => 75.8, 'median' => 78.6, 'sd_pos1' => 81.4, 'sd_pos2' => 84.2, 'sd_pos3' => 87.0],
            ['umur_bulan' => 17, 'sd_neg3' => 71.1, 'sd_neg2' => 74.0, 'sd_neg1' => 76.8, 'median' => 79.7, 'sd_pos1' => 82.5, 'sd_pos2' => 85.4, 'sd_pos3' => 88.2],
            ['umur_bulan' => 18, 'sd_neg3' => 72.0, 'sd_neg2' => 74.9, 'sd_neg1' => 77.8, 'median' => 80.7, 'sd_pos1' => 83.6, 'sd_pos2' => 86.5, 'sd_pos3' => 89.4],
            ['umur_bulan' => 19, 'sd_neg3' => 72.8, 'sd_neg2' => 75.8, 'sd_neg1' => 78.8, 'median' => 81.7, 'sd_pos1' => 84.7, 'sd_pos2' => 87.6, 'sd_pos3' => 90.6],
            ['umur_bulan' => 20, 'sd_neg3' => 73.7, 'sd_neg2' => 76.7, 'sd_neg1' => 79.7, 'median' => 82.7, 'sd_pos1' => 85.7, 'sd_pos2' => 88.7, 'sd_pos3' => 91.7],
            ['umur_bulan' => 21, 'sd_neg3' => 74.5, 'sd_neg2' => 77.5, 'sd_neg1' => 80.6, 'median' => 83.7, 'sd_pos1' => 86.7, 'sd_pos2' => 89.8, 'sd_pos3' => 92.9],
            ['umur_bulan' => 22, 'sd_neg3' => 75.2, 'sd_neg2' => 78.4, 'sd_neg1' => 81.5, 'median' => 84.6, 'sd_pos1' => 87.7, 'sd_pos2' => 90.8, 'sd_pos3' => 94.0],
            ['umur_bulan' => 23, 'sd_neg3' => 76.0, 'sd_neg2' => 79.2, 'sd_neg1' => 82.3, 'median' => 85.5, 'sd_pos1' => 88.7, 'sd_pos2' => 91.9, 'sd_pos3' => 95.0],
            ['umur_bulan' => 24, 'sd_neg3' => 76.7, 'sd_neg2' => 80.0, 'sd_neg1' => 83.2, 'median' => 86.4, 'sd_pos1' => 89.6, 'sd_pos2' => 92.9, 'sd_pos3' => 96.1],
        ];
    }
}
