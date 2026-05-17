<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Prior Naive Bayes</h3>
                <h6 class="font-weight-normal mb-0 text-muted">Probabilitas awal setiap kelas H1, H2, dan H3.</h6>
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
                        <h4 class="card-title">Tabel Prior</h4>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kelas</th>
                                        <th>Label</th>
                                        <th>Probabilitas</th>
                                        <th>Rekomendasi</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($tb_prior ?? [] as $row): ?>
                                        <tr>
                                            <form action="<?= base_url('admin/updatePrior/' . $row['id_prior']); ?>" method="post">
                                                <?= csrf_field() ?>
                                                <td><?= esc((string) $no++); ?></td>
                                                <td><strong><?= esc($row['kelas']); ?></strong></td>
                                                <td><input class="form-control form-control-sm" type="text" name="label" value="<?= esc($row['label'], 'attr'); ?>" required></td>
                                                <td><input class="form-control form-control-sm" type="number" step="0.00001" min="0.00001" max="1" name="probabilitas" value="<?= esc((string) $row['probabilitas'], 'attr'); ?>" required></td>
                                                <td><textarea class="form-control form-control-sm" name="rekomendasi" rows="3"><?= esc($row['rekomendasi'] ?? ''); ?></textarea></td>
                                                <td><button class="btn btn-primary btn-sm" type="submit">Simpan</button></td>
                                            </form>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($tb_prior)): ?>
                                        <tr><td colspan="6" class="text-center text-muted py-4">Data prior belum tersedia.</td></tr>
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
