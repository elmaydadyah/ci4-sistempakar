<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Hasil Diagnosa</h3>
                        <h6 class="font-weight-normal mb-0 text-muted">Riwayat hasil konsultasi Z-Score + Naive Bayes H1/H2/H3.</h6>
                    </div>
                    <div class="col-12 col-xl-4 text-xl-right">
                        <a class="btn btn-primary" href="<?= base_url('konsultasi') ?>">Konsultasi Baru</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Hasil Diagnosa</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Anak</th>
                                        <th>NIK</th>
                                        <th>Umur</th>
                                        <th>Z-Score</th>
                                        <th>Kelas NB</th>
                                        <th>Posterior</th>
                                        <th>Gejala Z-Score</th>
                                        <th>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_hasil_diagnosa) && is_array($tb_hasil_diagnosa)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_hasil_diagnosa as $hasil): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><strong><?= esc($hasil['nama'] ?? '-'); ?></strong></td>
                                                <td><?= esc($hasil['nik'] ?? '-'); ?></td>
                                                <td><?= esc((string) ($hasil['umur'] ?? '-')); ?> bulan</td>
                                                <td>
                                                    <div class="small">BB/U: <?= esc($hasil['kategori_bb_u'] ?? '-'); ?> <?= isset($hasil['zs_bb_u']) ? '(' . esc((string) $hasil['zs_bb_u']) . ')' : ''; ?></div>
                                                    <div class="small">TB/U: <?= esc($hasil['kategori_tb_u'] ?? '-'); ?> <?= isset($hasil['zs_tb_u']) ? '(' . esc((string) $hasil['zs_tb_u']) . ')' : ''; ?></div>
                                                    <div class="small">BB/TB: <?= esc($hasil['kategori_bb_tb'] ?? '-'); ?> <?= isset($hasil['zs_bb_tb']) ? '(' . esc((string) $hasil['zs_bb_tb']) . ')' : ''; ?></div>
                                                </td>
                                                <td><?= esc($hasil['nama_kasus'] ?? 'Belum ada indikasi'); ?></td>
                                                <td>
                                                    <span class="badge badge-primary"><?= esc((string) ($hasil['persentase'] ?? 0)); ?>%</span>
                                                </td>
                                                <td><?= esc((string) ($hasil['jumlah_gejala'] ?? 0)); ?> gejala</td>
                                                <td><?= esc($hasil['created_at'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted py-4">Belum ada hasil diagnosa tersimpan.</td>
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
