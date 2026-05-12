<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Halaman Index Kasus Gejala</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- partial -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted mb-3">Halaman ini menampilkan relasi gejala lama. Perhitungan terbaru memakai data status gizi untuk Naive Bayes dan menu Certainty Factor untuk bobot keyakinan.</p>
                        <h4 class="card-title">Relasi Gejala</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Hasil NB</th>
                                        <th>Gejala</th>
                                        <th>Bobot CF</th>
                                        <th>Aksi</th>
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
                                                <td>
                                                    <button class="btn btn-primary btn-sm">Edit</button>
                                                    <button class="btn btn-danger btn-sm">Delete</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7">Data tidak tersedia.</td>
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
