<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Proses Diagnosa</h3>
                        <h6 class="font-weight-normal mb-0 text-muted">Pantau alur konsultasi dari input balita, Z-Score, gejala otomatis, sampai Naive Bayes H1/H2/H3.</h6>
                    </div>
                    <div class="col-12 col-xl-4 text-xl-right">
                        <a class="btn btn-primary" href="<?= base_url('konsultasi') ?>">Buka Form Diagnosa</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php
            $cards = [
                ['label' => 'Data Anak', 'value' => $total_anak ?? 0, 'href' => base_url('adminanak'), 'icon' => 'ti-user'],
                ['label' => 'Data Latih', 'value' => $total_data_latih ?? 0, 'href' => base_url('adminstatusgizi'), 'icon' => 'ti-clipboard'],
                ['label' => 'H1 Risiko Tinggi', 'value' => $total_h1 ?? 0, 'href' => base_url('adminhasildiagnosa'), 'icon' => 'ti-alert'],
                ['label' => 'H2 Risiko Rendah', 'value' => $total_h2 ?? 0, 'href' => base_url('adminhasildiagnosa'), 'icon' => 'ti-pulse'],
                ['label' => 'H3 Tidak Risiko', 'value' => $total_h3 ?? 0, 'href' => base_url('adminhasildiagnosa'), 'icon' => 'ti-check-box'],
                ['label' => 'Hasil Diagnosa', 'value' => $total_hasil ?? 0, 'href' => base_url('adminhasildiagnosa'), 'icon' => 'ti-pulse'],
            ];
            ?>
            <?php foreach ($cards as $card): ?>
                <div class="col-md-3 grid-margin stretch-card">
                    <a class="card text-decoration-none w-100" href="<?= esc($card['href'], 'attr'); ?>">
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-2"><?= esc($card['label']); ?></p>
                                <h3 class="font-weight-bold mb-0"><?= esc((string) $card['value']); ?></h3>
                            </div>
                            <i class="<?= esc($card['icon'], 'attr'); ?> h3 text-primary mb-0"></i>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="row">
            <div class="col-lg-5 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Alur Diagnosa</h4>
                        <div class="admin-empty-state text-left">
                            <p class="mb-2"><strong>1. User</strong> menginput data balita: identitas, BB, TB, lingkar lengan, lingkar kepala, riwayat, pola makan, dan tempat tinggal.</p>
                            <p class="mb-2"><strong>2. Sistem</strong> menghitung Z-Score BB/U, TB/U, dan BB/TB lalu membuat kategori status gizi.</p>
                            <p class="mb-2"><strong>3. Sistem</strong> mengonversi kategori Z-Score menjadi gejala untuk algoritma.</p>
                            <p class="mb-0"><strong>4. Naive Bayes</strong> menghitung prior, likelihood, posterior, lalu memilih H1, H2, atau H3.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Hasil Terbaru</h4>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table" data-page-length="5">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Kelas NB</th>
                                        <th>Posterior</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_hasil)): ?>
                                        <?php foreach ($recent_hasil as $hasil): ?>
                                            <tr>
                                                <td><?= esc($hasil['nama'] ?? '-'); ?></td>
                                                <td><?= esc($hasil['nama_kasus'] ?? '-'); ?></td>
                                                <td><?= esc((string) ($hasil['persentase'] ?? 0)); ?>%</td>
                                                <td><?= esc($hasil['created_at'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="4" class="text-center text-muted py-4">Belum ada hasil diagnosa.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Data Anak Terbaru</h4>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table" data-page-length="5">
                                <thead>
                                    <tr>
                                        <th>Nama Anak</th>
                                        <th>NIK</th>
                                        <th>Umur</th>
                                        <th>BB</th>
                                        <th>TB</th>
                                        <th>TB/U</th>
                                        <th>Orang Tua</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($recent_anak)): ?>
                                        <?php foreach ($recent_anak as $anak): ?>
                                            <tr>
                                                <td><?= esc($anak['nama_anak'] ?? '-'); ?></td>
                                                <td><?= esc($anak['nik'] ?? '-'); ?></td>
                                                <td><?= esc((string) ($anak['umur_bulan'] ?? '-')); ?> bulan</td>
                                                <td><?= esc((string) ($anak['berat_badan'] ?? '-')); ?> kg</td>
                                                <td><?= esc((string) ($anak['tinggi_badan'] ?? '-')); ?> cm</td>
                                                <td><?= esc($anak['kategori_tb_u'] ?? '-'); ?></td>
                                                <td><?= esc($anak['nama_ortu'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="7" class="text-center text-muted py-4">Belum ada data anak.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?= $this->include('layout/dashboard/footer') ?>
