<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Data Hipotesis</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Hipotesis</h4>
                        <p class="text-muted mb-3">Data hipotesis risiko stunting dan solusi berdasarkan tabel Excel.</p>

                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Hipotesis</th>
                                        <th>Risiko Stunting pada Balita</th>
                                        <th>Solusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_hipotesis) && is_array($tb_hipotesis)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_hipotesis as $hipotesis): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= esc($hipotesis['kode_hipotesis'] ?? '-'); ?></td>
                                                <td><?= esc($hipotesis['risiko_stunting'] ?? '-'); ?></td>
                                                <td><?= esc($hipotesis['solusi'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
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
