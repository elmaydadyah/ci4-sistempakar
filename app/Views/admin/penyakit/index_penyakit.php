<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-md-12 grid-margin">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-8 mb-2 mb-xl-0">
                        <h3 class="font-weight-bold">Data Penyakit</h3>
                    </div>
                    <div class="col-12 col-xl-4 text-xl-right">
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createPenyakitModal">
                            Tambah Penyakit
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Tabel Penyakit</h4>

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
                                        <th>ID Penyakit</th>
                                        <th>Nama Penyakit</th>
                                        <th>Deskripsi</th>
                                        <th>Solusi</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($tb_penyakit) && is_array($tb_penyakit)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_penyakit as $penyakit): ?>
                                            <tr>
                                                <td><?= $no++; ?></td>
                                                <td><?= esc($penyakit['id_kasus']); ?></td>
                                                <td><?= esc($penyakit['nama_kasus']); ?></td>
                                                <td><div class="admin-table-text"><?= esc($penyakit['deskripsi']); ?></div></td>
                                                <td><div class="admin-table-text"><?= esc($penyakit['solusi']); ?></div></td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                        data-target="#editPenyakitModal"
                                                        data-id="<?= esc($penyakit['id_kasus'], 'attr'); ?>"
                                                        data-nama="<?= esc($penyakit['nama_kasus'], 'attr'); ?>"
                                                        data-deskripsi="<?= esc($penyakit['deskripsi'], 'attr'); ?>"
                                                        data-solusi="<?= esc($penyakit['solusi'], 'attr'); ?>">Edit</button>
                                                    <a href="<?= base_url('/admin/deletePenyakit/' . $penyakit['id_kasus']); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data penyakit ini?');">Delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
                                            <td colspan="6">Data tidak tersedia.</td>
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

    <div class="modal fade" id="createPenyakitModal" tabindex="-1" role="dialog" aria-labelledby="createPenyakitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="<?= base_url('/admin/createPenyakit'); ?>">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPenyakitModalLabel">Tambah Penyakit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create-nama-kasus">Nama Penyakit</label>
                            <input type="text" class="form-control" id="create-nama-kasus" name="nama_kasus" required>
                        </div>
                        <div class="form-group">
                            <label for="create-deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="create-deskripsi" name="deskripsi" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="create-solusi">Solusi</label>
                            <textarea class="form-control" id="create-solusi" name="solusi" rows="4"></textarea>
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

    <div class="modal fade" id="editPenyakitModal" tabindex="-1" role="dialog" aria-labelledby="editPenyakitModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="editPenyakitForm">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPenyakitModalLabel">Edit Penyakit</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-nama-kasus">Nama Penyakit</label>
                            <input type="text" class="form-control" id="edit-nama-kasus" name="nama_kasus" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-deskripsi">Deskripsi</label>
                            <textarea class="form-control" id="edit-deskripsi" name="deskripsi" rows="4"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-solusi">Solusi</label>
                            <textarea class="form-control" id="edit-solusi" name="solusi" rows="4"></textarea>
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
            $('#editPenyakitModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#editPenyakitForm').attr('action', '<?= base_url('/admin/updatePenyakit'); ?>/' + id);
                $('#edit-nama-kasus').val(button.data('nama'));
                $('#edit-deskripsi').val(button.data('deskripsi'));
                $('#edit-solusi').val(button.data('solusi'));
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
