<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Standar Antropometri</h3>
                <h6 class="font-weight-normal mb-0 text-muted">Referensi median dan SD untuk perhitungan Z-Score BB/U, TB/U, dan BB/TB.</h6>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Standar</h4>
                        <p class="text-muted">Menampilkan 300 baris pertama. Edit nilai median dan SD sesuai rujukan yang dipakai.</p>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Indikator</th>
                                        <th>JK</th>
                                        <th>Umur</th>
                                        <th>Tinggi</th>
                                        <th>Median</th>
                                        <th>SD</th>
                                        <th>Sumber</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tb_standar ?? [] as $row): ?>
                                        <tr>
                                            <form action="<?= base_url('admin/updateStandar/' . $row['id_standar']); ?>" method="post">
                                                <?= csrf_field() ?>
                                                <td><?= esc($row['indikator']); ?></td>
                                                <td><?= esc($row['jenis_kelamin']); ?></td>
                                                <td><?= $row['umur_bulan'] !== null ? esc((string) $row['umur_bulan']) . ' bulan' : '-'; ?></td>
                                                <td><?= $row['tinggi_cm'] !== null ? esc((string) $row['tinggi_cm']) . ' cm' : '-'; ?></td>
                                                <td><input class="form-control form-control-sm" type="number" step="0.01" name="median" value="<?= esc((string) $row['median'], 'attr'); ?>" required></td>
                                                <td><input class="form-control form-control-sm" type="number" step="0.01" min="0.01" name="sd" value="<?= esc((string) $row['sd'], 'attr'); ?>" required></td>
                                                <td>
                                                    <input class="form-control form-control-sm mb-1" type="text" name="sumber" value="<?= esc($row['sumber'] ?? '', 'attr'); ?>">
                                                    <input class="form-control form-control-sm" type="text" name="catatan" value="<?= esc($row['catatan'] ?? '', 'attr'); ?>" placeholder="Catatan">
                                                </td>
                                                <td><button class="btn btn-primary btn-sm" type="submit">Simpan</button></td>
                                            </form>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($tb_standar)): ?>
                                        <tr><td colspan="8" class="text-center text-muted py-4">Data standar belum tersedia.</td></tr>
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
