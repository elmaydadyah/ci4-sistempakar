<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
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
                        <p class="text-muted mb-3">Probabilitas awal setiap kelas H1, H2, dan H3.</p>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kelas</th>
                                        <th>Label</th>
                                        <th>Probabilitas</th>
                                        <th>Rekomendasi</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; ?>
                                    <?php foreach ($tb_prior ?? [] as $row): ?>
                                        <tr>
                                            <td><?= esc((string) $no++); ?></td>
                                            <td><strong><?= esc($row['kelas']); ?></strong></td>
                                            <td><?= esc($row['label']); ?></td>
                                            <td><?= esc(number_format((float) $row['probabilitas'], 5, '.', '')); ?></td>
                                            <td><div class="admin-table-text"><?= esc($row['rekomendasi'] ?? '-'); ?></div></td>
                                            <td>
                                                <div class="admin-table-actions">
                                                    <button class="btn btn-primary btn-sm"
                                                        type="button"
                                                        data-toggle="modal"
                                                        data-target="#editPriorModal"
                                                        data-id="<?= esc($row['id_prior'], 'attr'); ?>"
                                                        data-kelas="<?= esc($row['kelas'], 'attr'); ?>"
                                                        data-label="<?= esc($row['label'], 'attr'); ?>"
                                                        data-probabilitas="<?= esc((string) $row['probabilitas'], 'attr'); ?>"
                                                        data-rekomendasi="<?= esc($row['rekomendasi'] ?? '', 'attr'); ?>">
                                                        Edit
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($tb_prior)): ?>
                                        <tr class="admin-empty-row"><td colspan="6" class="text-center text-muted py-4">Data prior belum tersedia.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPriorModal" tabindex="-1" role="dialog" aria-labelledby="editPriorModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="editPriorForm">
                <?= csrf_field() ?>
                <input type="hidden" name="redirect_to" value="<?= esc((string) current_url(true), 'attr'); ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPriorModalLabel">Edit Prior Naive Bayes</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-prior-kelas">Kelas</label>
                            <input type="text" class="form-control" id="edit-prior-kelas" readonly>
                        </div>
                        <div class="form-group">
                            <label for="edit-prior-label">Label</label>
                            <input type="text" class="form-control" id="edit-prior-label" name="label" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-prior-probabilitas">Probabilitas</label>
                            <input type="number" class="form-control" id="edit-prior-probabilitas" name="probabilitas" step="0.00001" min="0.00001" max="1" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-prior-rekomendasi">Rekomendasi</label>
                            <textarea class="form-control" id="edit-prior-rekomendasi" name="rekomendasi" rows="4"></textarea>
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
            $('#editPriorModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#editPriorForm').attr('action', '<?= base_url('admin/updatePrior'); ?>/' + id);
                $('#edit-prior-kelas').val(button.data('kelas'));
                $('#edit-prior-label').val(button.data('label'));
                $('#edit-prior-probabilitas').val(button.data('probabilitas'));
                $('#edit-prior-rekomendasi').val(button.data('rekomendasi'));
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
