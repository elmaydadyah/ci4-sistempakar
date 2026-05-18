<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Data Certainty Factor</h3>
                        <h6 class="font-weight-normal mb-0 text-muted">Kelola bobot keyakinan gejala yang dipakai untuk menghitung persentase CF.</h6>
                    </div>
                    <div class="col-12 col-xl-4 text-xl-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createCfModal">
                            Tambah CF
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Bobot Keyakinan Gejala</h4>
                        <p class="text-muted mb-3">Bobot CF menggunakan nilai 0 sampai 1. Contoh: 0.6 untuk gejala cukup kuat, 0.8 untuk gejala kuat.</p>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Gejala</th>
                                        <th>Bobot CF</th>
                                        <th>Keterangan</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_cf) && is_array($tb_cf)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_cf as $cf): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= esc($cf['nama_gejala'] ?? ('Gejala #' . $cf['id_gejala'])); ?></td>
                                                <td><?= esc(number_format((float) $cf['bobot_cf'], 2, '.', '')); ?></td>
                                                <td><div class="admin-table-text"><?= esc($cf['keterangan'] ?? '-'); ?></div></td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                    <button class="btn btn-primary btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#editCfModal"
                                                        data-id="<?= esc($cf['id_cf'], 'attr'); ?>"
                                                        data-gejala="<?= esc($cf['id_gejala'], 'attr'); ?>"
                                                        data-bobot="<?= esc($cf['bobot_cf'], 'attr'); ?>"
                                                        data-keterangan="<?= esc($cf['keterangan'] ?? '', 'attr'); ?>">Edit</button>
                                                    <a href="<?= base_url('/admin/deleteCf/' . $cf['id_cf']); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data CF ini?');">Delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="5">Data CF belum tersedia.</td>
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

    <div class="modal fade" id="createCfModal" tabindex="-1" role="dialog" aria-labelledby="createCfModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="<?= base_url('/admin/createCf'); ?>">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCfModalLabel">Tambah Certainty Factor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create-id-gejala">Gejala</label>
                            <select class="form-control" id="create-id-gejala" name="id_gejala" required>
                                <option value="">Pilih gejala</option>
                                <?php foreach ($tb_gejala ?? [] as $gejala): ?>
                                    <option value="<?= esc($gejala['id_gejala'], 'attr'); ?>"><?= esc($gejala['nama_gejala']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="create-bobot-cf">Bobot CF</label>
                            <input type="number" class="form-control" id="create-bobot-cf" name="bobot_cf" min="0" max="1" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="create-keterangan">Keterangan</label>
                            <textarea class="form-control" id="create-keterangan" name="keterangan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editCfModal" tabindex="-1" role="dialog" aria-labelledby="editCfModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="editCfForm">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editCfModalLabel">Edit Certainty Factor</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-id-gejala">Gejala</label>
                            <select class="form-control" id="edit-id-gejala" name="id_gejala" required>
                                <?php foreach ($tb_gejala ?? [] as $gejala): ?>
                                    <option value="<?= esc($gejala['id_gejala'], 'attr'); ?>"><?= esc($gejala['nama_gejala']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-bobot-cf">Bobot CF</label>
                            <input type="number" class="form-control" id="edit-bobot-cf" name="bobot_cf" min="0" max="1" step="0.01" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-keterangan">Keterangan</label>
                            <textarea class="form-control" id="edit-keterangan" name="keterangan" rows="3"></textarea>
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
            $('#editCfModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#editCfForm').attr('action', '<?= base_url('/admin/updateCf'); ?>/' + id);
                $('#edit-id-gejala').val(button.data('gejala'));
                $('#edit-bobot-cf').val(button.data('bobot'));
                $('#edit-keterangan').val(button.data('keterangan'));
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
