<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $db = db_connect();

        $stats = [
            'users' => $this->countTable($db, 'tb_users'),
            'gejala' => $this->countTable($db, 'tb_gejala'),
            'kasus' => $this->countTable($db, 'tb_kasus'),
            'relasi' => $this->countTable($db, 'tb_kons_detail'),
            'status_gizi' => $this->countTable($db, 'tb_anak_status_gizi'),
            'hasil_diagnosa' => $this->countTable($db, 'tb_hasil_diagnosa'),
            'standar_antropometri' => $this->countTable($db, 'tb_standar_antropometri'),
            'prior_nb' => $this->countTable($db, 'tb_naive_bayes_prior'),
            'likelihood_nb' => $this->countTable($db, 'tb_naive_bayes_likelihood'),
        ];

        $nutritionStatus = [
            'gizi_kurang' => 0,
            'pendek' => 0,
            'latest_upload' => null,
        ];
        $recentNutritionRows = [];
        $diagnosisDailyChart = $this->getEmptyDailyChart();
        $diagnosisClassDailyChart = $this->getEmptyClassDailyChart();
        $diagnosisClassTotalChart = $this->getEmptyClassTotalChart();
        $recentDiagnosisRows = [];

        if ($db->tableExists('tb_anak_status_gizi')) {
            $nutritionStatus['gizi_kurang'] = $db->table('tb_anak_status_gizi')
                ->whereIn('bb_tb', ['Gizi Kurang', 'Gizi Buruk'])
                ->countAllResults();
            $nutritionStatus['pendek'] = $db->table('tb_anak_status_gizi')
                ->whereIn('tb_u', ['Pendek', 'Sangat Pendek'])
                ->countAllResults();
            $nutritionStatus['latest_upload'] = $db->table('tb_anak_status_gizi')
                ->select('uploaded_file, created_at')
                ->where('uploaded_file IS NOT NULL', null, false)
                ->orderBy('created_at', 'DESC')
                ->get(1)
                ->getRowArray();
            $recentNutritionRows = $db->table('tb_anak_status_gizi')
                ->select('nama, nik, desa_kel, posyandu, bb_tb, tb_u, tanggal_pengukuran')
                ->orderBy('id_status_gizi', 'DESC')
                ->get(5)
                ->getResultArray();
        }

        if ($db->tableExists('tb_hasil_diagnosa')) {
            $diagnosisDailyChart = $this->getDiagnosisDailyChart($db);
            $diagnosisClassDailyChart = $this->getDiagnosisClassDailyChart($db);
            $diagnosisClassTotalChart = $this->getDiagnosisClassTotalChart($db);
            $recentDiagnosisRows = $db->table('tb_hasil_diagnosa')
                ->select('id_anak, nama, umur, nama_kasus, persentase, jumlah_gejala, created_at')
                ->orderBy('id_hasil_diagnosa', 'DESC')
                ->get(5)
                ->getResultArray();
        }

        return view('dashboard', [
            'dashboardStats' => $stats,
            'nutritionStatus' => $nutritionStatus,
            'recentNutritionRows' => $recentNutritionRows,
            'diagnosisDailyChart' => $diagnosisDailyChart,
            'diagnosisClassDailyChart' => $diagnosisClassDailyChart,
            'diagnosisClassTotalChart' => $diagnosisClassTotalChart,
            'recentDiagnosisRows' => $recentDiagnosisRows,
        ]);
    }

    private function countTable($db, string $table): int
    {
        if (!$db->tableExists($table)) {
            return 0;
        }

        return $db->table($table)->countAllResults();
    }

    private function getEmptyDailyChart(): array
    {
        $labels = [];
        $values = [];

        for ($i = 6; $i >= 0; $i--) {
            $labels[] = date('d M', strtotime("-{$i} days"));
            $values[] = 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getEmptyClassDailyChart(): array
    {
        $labels = [];
        $dateKeys = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dateKeys[$date] = [
                'H1' => 0,
                'H2' => 0,
                'H3' => 0,
            ];
            $labels[] = date('d M', strtotime($date));
        }

        return [
            'labels' => $labels,
            'datasets' => [
                'H1' => array_column($dateKeys, 'H1'),
                'H2' => array_column($dateKeys, 'H2'),
                'H3' => array_column($dateKeys, 'H3'),
            ],
        ];
    }

    private function getEmptyClassTotalChart(): array
    {
        return [
            'labels' => ['H1', 'H2', 'H3'],
            'values' => [0, 0, 0],
        ];
    }

    private function getDiagnosisDailyChart($db): array
    {
        $labels = [];
        $values = [];
        $dateKeys = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dateKeys[$date] = 0;
            $labels[] = date('d M', strtotime($date));
        }

        $rows = $db->table('tb_hasil_diagnosa')
            ->select('DATE(created_at) as tanggal, COUNT(*) as total', false)
            ->where('created_at >=', date('Y-m-d 00:00:00', strtotime('-6 days')))
            ->groupBy('DATE(created_at)')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $tanggal = $row['tanggal'] ?? null;
            if (isset($dateKeys[$tanggal])) {
                $dateKeys[$tanggal] = (int) ($row['total'] ?? 0);
            }
        }

        foreach ($dateKeys as $value) {
            $values[] = $value;
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function getDiagnosisClassDailyChart($db): array
    {
        $labels = [];
        $dateKeys = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $dateKeys[$date] = [
                'H1' => 0,
                'H2' => 0,
                'H3' => 0,
            ];
            $labels[] = date('d M', strtotime($date));
        }

        $rows = $db->table('tb_hasil_diagnosa')
            ->select('DATE(created_at) as tanggal, kelas_hasil, COUNT(*) as total', false)
            ->where('created_at >=', date('Y-m-d 00:00:00', strtotime('-6 days')))
            ->whereIn('kelas_hasil', ['H1', 'H2', 'H3'])
            ->groupBy('DATE(created_at)', false)
            ->groupBy('kelas_hasil')
            ->orderBy('tanggal', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $tanggal = $row['tanggal'] ?? null;
            $kelas = (string) ($row['kelas_hasil'] ?? '');

            if (isset($dateKeys[$tanggal][$kelas])) {
                $dateKeys[$tanggal][$kelas] = (int) ($row['total'] ?? 0);
            }
        }

        return [
            'labels' => $labels,
            'datasets' => [
                'H1' => array_column($dateKeys, 'H1'),
                'H2' => array_column($dateKeys, 'H2'),
                'H3' => array_column($dateKeys, 'H3'),
            ],
        ];
    }

    private function getDiagnosisClassTotalChart($db): array
    {
        $totals = [
            'H1' => 0,
            'H2' => 0,
            'H3' => 0,
        ];

        $rows = $db->table('tb_hasil_diagnosa')
            ->select('kelas_hasil, COUNT(*) as total')
            ->whereIn('kelas_hasil', ['H1', 'H2', 'H3'])
            ->groupBy('kelas_hasil')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $kelas = (string) ($row['kelas_hasil'] ?? '');
            if (array_key_exists($kelas, $totals)) {
                $totals[$kelas] = (int) ($row['total'] ?? 0);
            }
        }

        return [
            'labels' => ['H1', 'H2', 'H3'],
            'values' => array_values($totals),
        ];
    }
}
