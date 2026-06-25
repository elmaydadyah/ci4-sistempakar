<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <!-- partial -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-3">Halaman ini menampilkan relasi gejala lama. Perhitungan terbaru memakai data status gizi dan probabilitas untuk Teorema Bayes.</p>
                        <h4 class="card-title">Relasi Gejala Bayes</h4>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Hasil Bayes</th>
                                        <th>Gejala</th>
                                        <th>Nilai Relasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($tb_kasusgejala) && is_array($tb_kasusgejala)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_kasusgejala as $kasusgejala): ?>
                                            <tr>
                                                <td>
                                                    <?= $no++; ?>
                                                </td>
                                                <td>
                                                    <?= esc($kasusgejala['nama_kasus'] ?? ('Kasus #' . $kasusgejala['id_kasus'])); ?>
                                                </td>
                                                <td>
                                                    <?= esc($kasusgejala['nama_gejala'] ?? ('Gejala #' . $kasusgejala['id_gejala'])); ?>
                                                </td>
                                                <td>
                                                    <?= esc((string) $kasusgejala['nilai']); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="4">Data tidak tersedia.</td>
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
