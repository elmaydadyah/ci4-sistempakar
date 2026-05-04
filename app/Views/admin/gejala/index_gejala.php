<?= $this->include('layout/header') ?>
<?= $this->include('layout/navbar') ?>
<?= $this->include('layout/sidebar') ?>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Halaman Index Users</h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- partial -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Gejala</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Id Gejala</th>
                                        <th>Nama Gejala</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($tb_gejala) && is_array($tb_gejala)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_gejala as $gejala): ?>
                                            <tr>
                                                <td>
                                                    <?= $no++; ?>
                                                </td>
                                                <td>
                                                    <?= $gejala['id_gejala']; ?>
                                                </td>
                                                <td>
                                                    <?= $gejala['nama_gejala']; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm">Edit</button>
                                                    <button class="btn btn-danger btn-sm">Delete</button>
                                                </td>
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

    <?= $this->include('layout/footer') ?>