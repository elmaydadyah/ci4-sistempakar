<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <h3 class="font-weight-bold">Likelihood Naive Bayes</h3>
                <h6 class="font-weight-normal mb-0 text-muted">Peluang kategori Z-Score terhadap kelas H1, H2, dan H3.</h6>
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
                        <h4 class="card-title">Tabel Likelihood</h4>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>Indikator</th>
                                        <th>Kategori</th>
                                        <th>Kelas</th>
                                        <th>Probabilitas</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tb_likelihood ?? [] as $row): ?>
                                        <tr>
                                            <td><?= esc($row['indikator']); ?></td>
                                            <td><?= esc($row['kategori']); ?></td>
                                            <td><strong><?= esc($row['kelas']); ?></strong></td>
                                            <td><?= esc(number_format((float) $row['probabilitas'], 5, '.', '')); ?></td>
                                            <td>
                                                <div class="admin-table-actions">
                                                    <button class="btn btn-primary btn-sm"
                                                        type="button"
                                                        data-toggle="modal"
                                                        data-target="#editLikelihoodModal"
                                                        data-id="<?= esc($row['id_likelihood'], 'attr'); ?>"
                                                        data-indikator="<?= esc($row['indikator'], 'attr'); ?>"
                                                        data-kategori="<?= esc($row['kategori'], 'attr'); ?>"
                                                        data-kelas="<?= esc($row['kelas'], 'attr'); ?>"
                                                        data-probabilitas="<?= esc((string) $row['probabilitas'], 'attr'); ?>">
                                                        Edit
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($tb_likelihood)): ?>
                                        <tr class="admin-empty-row"><td colspan="5" class="text-center text-muted py-4">Data likelihood belum tersedia.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editLikelihoodModal" tabindex="-1" role="dialog" aria-labelledby="editLikelihoodModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="editLikelihoodForm">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="<?= esc((string) current_url(true), 'attr'); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editLikelihoodModalLabel">Edit Likelihood Naive Bayes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-likelihood-indikator">Indikator</label>
                            <input type="text" class="form-control" id="edit-likelihood-indikator" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-likelihood-kategori">Kategori</label>
                            <input type="text" class="form-control" id="edit-likelihood-kategori" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-likelihood-kelas">Kelas</label>
                            <input type="text" class="form-control" id="edit-likelihood-kelas" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-likelihood-probabilitas">Probabilitas</label>
                            <input type="number" class="form-control" id="edit-likelihood-probabilitas" name="probabilitas" step="0.00001" min="0.00001" max="1" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('#editLikelihoodModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#editLikelihoodForm').attr('action', '<?= base_url('admin/updateLikelihood'); ?>/' + id);
                $('#edit-likelihood-indikator').val(button.data('indikator'));
                $('#edit-likelihood-kategori').val(button.data('kategori'));
                $('#edit-likelihood-kelas').val(button.data('kelas'));
                $('#edit-likelihood-probabilitas').val(button.data('probabilitas'));
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
