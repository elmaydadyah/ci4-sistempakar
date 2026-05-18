<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Data Gejala</h3>
                    </div>
                    <div class="col-12 col-xl-4 text-xl-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createGejalaModal">
                            Tambah Gejala
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Gejala</h4>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success">
                                <?= session()->getFlashdata('success'); ?>
                            </div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?= session()->getFlashdata('error'); ?>
                            </div>
                        <?php endif; ?>

                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Kode Gejala</th>
                                        <th>Nama Gejala</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_gejala) && is_array($tb_gejala)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_gejala as $gejala): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= esc($gejala['kode_gejala'] ?? '-'); ?></td>
                                                <td><?= esc($gejala['nama_gejala']); ?></td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                        data-target="#editGejalaModal"
                                                        data-id="<?= esc($gejala['id_gejala'], 'attr'); ?>"
                                                        data-kode="<?= esc($gejala['kode_gejala'] ?? '', 'attr'); ?>"
                                                        data-nama="<?= esc($gejala['nama_gejala'], 'attr'); ?>">Edit</button>
                                                    <a href="<?= base_url('/admin/deleteGejala/' . $gejala['id_gejala']); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data gejala ini?');">Delete</a>
                                                    </div>
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

    <div class="modal fade" id="createGejalaModal" tabindex="-1" role="dialog" aria-labelledby="createGejalaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="<?= base_url('/admin/createGejala'); ?>">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createGejalaModalLabel">Tambah Gejala</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create-kode-gejala">Kode Gejala</label>
                            <input type="text" class="form-control" id="create-kode-gejala" name="kode_gejala" required>
                        </div>
                        <div class="form-group">
                            <label for="create-nama-gejala">Nama Gejala</label>
                            <input type="text" class="form-control" id="create-nama-gejala" name="nama_gejala" required>
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

    <div class="modal fade" id="editGejalaModal" tabindex="-1" role="dialog" aria-labelledby="editGejalaModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="editGejalaForm">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editGejalaModalLabel">Edit Gejala</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-kode-gejala">Kode Gejala</label>
                            <input type="text" class="form-control" id="edit-kode-gejala" name="kode_gejala" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-nama-gejala">Nama Gejala</label>
                            <input type="text" class="form-control" id="edit-nama-gejala" name="nama_gejala" required>
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
            $('#editGejalaModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#editGejalaForm').attr('action', '<?= base_url('/admin/updateGejala'); ?>/' + id);
                $('#edit-kode-gejala').val(button.data('kode'));
                $('#edit-nama-gejala').val(button.data('nama'));
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
