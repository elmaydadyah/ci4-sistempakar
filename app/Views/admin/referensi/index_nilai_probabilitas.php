<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Probabilitas Gejala</h4>
                        <p class="text-muted mb-3">Peluang setiap gejala terhadap hipotesis H1, H2, dan H3.</p>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode</th>
                                        <th>Gejala</th>
                                        <th>H1</th>
                                        <th>H2</th>
                                        <th>H3</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_nilai_probabilitas) && is_array($tb_nilai_probabilitas)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_nilai_probabilitas as $row): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><strong><?= esc($row['kode_gejala'] ?? '-'); ?></strong></td>
                                                <td><?= esc($row['nama_gejala'] ?? '-'); ?></td>
                                                <td><?= esc($row['H1'] !== null ? number_format((float) $row['H1'], 2) : '-'); ?></td>
                                                <td><?= esc($row['H2'] !== null ? number_format((float) $row['H2'], 2) : '-'); ?></td>
                                                <td><?= esc($row['H3'] !== null ? number_format((float) $row['H3'], 2) : '-'); ?></td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                        <a class="btn btn-primary btn-sm" href="<?= base_url('adminlikelihood'); ?>">Kelola Probabilitas Antropometri</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="7" class="text-center text-muted py-4">Data probabilitas gejala belum tersedia.</td>
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
