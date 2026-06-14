<?php
$roleOptions = [
    'admin1' => 'Admin 1 - Full akses',
    'admin2' => 'Admin 2 - Data operasional',
    'admin3' => 'Admin 3 - Lihat data',
];
$normalizeRole = static function ($role): string {
    return match (strtolower(trim((string) $role))) {
        'admin2' => 'admin2',
        'admin3', '1', 'user' => 'admin3',
        default => 'admin1',
    };
};
?>
<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<!-- partial -->
<div class="main-panel">
    <div class="content-wrapper">
        <!-- partial -->
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card">
                    <div class="card-body">
                        <div class="admin-table-toolbar">
                            <div>
                                <h4 class="card-title mb-1">Tabel Users</h4>
                                <p class="text-muted mb-0">Kelola akun admin yang bisa masuk ke dashboard.</p>
                            </div>
                            <button class="btn btn-primary" data-toggle="modal" data-target="#createUserModal">
                                Tambah Admin
                            </button>
                        </div>
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
                                        <th>Id User</th>
                                        <th>Nama User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>created At</th>
                                        <th class="admin-no-sort">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($tb_users) && is_array($tb_users)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_users as $user): ?>
                                            <?php
                                                $roleValue = $normalizeRole($user['role'] ?? 'admin1');
                                                $roleLabel = $roleOptions[$roleValue] ?? 'Admin 1 - Full akses';
                                            ?>
                                            <tr>
                                                <td>
                                                    <?= $no++; ?>
                                                </td>
                                                <td>
                                                    <?= esc((string) $user['id_users']); ?>
                                                </td>
                                                <td>
                                                    <?= esc($user['nama']); ?>
                                                </td>
                                                <td>
                                                    <?= esc($user['email']); ?>
                                                </td>
                                                <td>
                                                    <?= esc($roleLabel); ?>
                                                </td>
                                                <td>
                                                    <?= esc((string) ($user['created_at'] ?? '-')); ?>
                                                </td>
                                                <td>
                                                    <div class="admin-table-actions">
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                        data-target="#editUserModal"
                                                        data-id="<?= $user['id_users']; ?>"
                                                        data-nama="<?= esc($user['nama']); ?>"
                                                        data-email="<?= esc($user['email']); ?>"
                                                        data-role="<?= esc($roleValue); ?>">Edit</button>
                                                    <a href="<?= base_url('/admin/deleteUser/' . $user['id_users']); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data user ini?');">Delete</a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr class="admin-empty-row">
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

    <div class="modal fade" id="createUserModal" tabindex="-1" role="dialog" aria-labelledby="createUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" action="<?= base_url('/admin/createUser'); ?>" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createUserModalLabel">Tambah Admin</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="create-nama">Nama Admin</label>
                            <input type="text" class="form-control" id="create-nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="create-email">Email</label>
                            <input type="email" class="form-control" id="create-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="create-password">Password</label>
                            <input type="password" class="form-control" id="create-password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="create-role">Role</label>
                            <select class="form-control" id="create-role" name="role" required>
                                <?php foreach ($roleOptions as $roleValue => $roleLabel): ?>
                                    <option value="<?= esc($roleValue, 'attr'); ?>"><?= esc($roleLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="create-foto">Foto Profil</label>
                            <input type="file" class="form-control" id="create-foto" name="foto" accept="image/png,image/jpeg">
                        </div>
                        <small class="text-muted">Admin 1 memiliki akses penuh. Admin 2 dan Admin 3 dibatasi sesuai menu operasional.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="editUserModal" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form method="post" id="editUserForm" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit-nama">Nama User</label>
                            <input type="text" class="form-control" id="edit-nama" name="nama" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="edit-role">Role</label>
                            <select class="form-control" id="edit-role" name="role" required>
                                <?php foreach ($roleOptions as $roleValue => $roleLabel): ?>
                                    <option value="<?= esc($roleValue, 'attr'); ?>"><?= esc($roleLabel); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-password">Password Baru</label>
                            <input type="password" class="form-control" id="edit-password" name="password"
                                placeholder="Kosongkan jika tidak ingin mengganti password">
                        </div>
                        <div class="form-group">
                            <label for="edit-foto">Foto Profil</label>
                            <input type="file" class="form-control" id="edit-foto" name="foto" accept="image/png,image/jpeg">
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
            $('#editUserModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#editUserForm').attr('action', '<?= base_url('/admin/updateUser'); ?>/' + id);
                $('#edit-nama').val(button.data('nama'));
                $('#edit-email').val(button.data('email'));
                $('#edit-role').val(button.data('role'));
                $('#edit-password').val('');
                $('#edit-foto').val('');
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
