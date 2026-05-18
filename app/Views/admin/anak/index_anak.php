<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Data Anak</h3>
                        <h6 class="font-weight-normal mb-0 text-muted">Data balita dari konsultasi beserta hasil Z-Score yang dikonversi menjadi gejala.</h6>
                    </div>
                    <div class="col-12 col-xl-4 text-xl-right">
                        <a class="btn btn-primary" href="<?= base_url('konsultasi') ?>">Tambah dari Konseling</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Anak</h4>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Nama Anak</th>
                                        <th>NIK</th>
                                        <th>JK</th>
                                        <th>Umur</th>
                                        <th>BB</th>
                                        <th>TB</th>
                                        <th>Z-Score</th>
                                        <th>Orang Tua</th>
                                        <th>Tempat Tinggal</th>
                                        <th>Alamat</th>
                                        <th>Dibuat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_anak) && is_array($tb_anak)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_anak as $anak): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td>
                                                    <strong><?= esc($anak['nama_anak'] ?? '-'); ?></strong>
                                                    <div class="text-muted small"><?= esc($anak['tanggal_lahir'] ?? '-'); ?></div>
                                                </td>
                                                <td><?= esc($anak['nik'] ?? '-'); ?></td>
                                                <td><?= esc($anak['jenis_kelamin'] ?? ($anak['jk_anak'] ?? '-')); ?></td>
                                                <td><?= esc((string) ($anak['umur_bulan'] ?? $anak['umur_anak'] ?? '-')); ?> bulan</td>
                                                <td><?= esc((string) ($anak['berat_badan'] ?? $anak['berat_anak'] ?? '-')); ?> kg</td>
                                                <td><?= esc((string) ($anak['tinggi_badan'] ?? $anak['tinggi_anak'] ?? '-')); ?> cm</td>
                                                <td>
                                                    <div class="small">BB/U: <?= esc($anak['kategori_bb_u'] ?? '-'); ?> <?= isset($anak['zs_bb_u']) ? '(' . esc((string) $anak['zs_bb_u']) . ')' : ''; ?></div>
                                                    <div class="small">TB/U: <?= esc($anak['kategori_tb_u'] ?? '-'); ?> <?= isset($anak['zs_tb_u']) ? '(' . esc((string) $anak['zs_tb_u']) . ')' : ''; ?></div>
                                                    <div class="small">BB/TB: <?= esc($anak['kategori_bb_tb'] ?? '-'); ?> <?= isset($anak['zs_bb_tb']) ? '(' . esc((string) $anak['zs_bb_tb']) . ')' : ''; ?></div>
                                                </td>
                                                <td><?= esc($anak['nama_ortu'] ?? '-'); ?></td>
                                                <td><?= esc($anak['tempat_tinggal'] ?? '-'); ?></td>
                                                <td><?= esc($anak['alamat'] ?? '-'); ?></td>
                                                <td><?= esc($anak['created_at'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="12" class="text-center text-muted py-4">Belum ada data anak dari form konseling.</td>
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
