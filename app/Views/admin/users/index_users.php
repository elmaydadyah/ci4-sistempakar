<?php
$roleOptions = $roleOptions ?? [
    'admin1' => 'Admin 1 - Full akses',
    'admin2' => 'Admin 2 - Data operasional',
    'admin3' => 'Admin 3 - Lihat data',
];
$permissionRows = $permissionRows ?? [
    ['key' => 'dashboard', 'menu' => 'Dashboard', 'category' => '-'],
    ['key' => 'anak', 'menu' => 'Data Anak', 'category' => 'Data Utama'],
    ['key' => 'statusgizi', 'menu' => 'Data Terdahulu', 'category' => 'Data Utama'],
    ['key' => 'hasildiagnosa', 'menu' => 'Hasil Diagnosa', 'category' => 'Data Utama'],
    ['key' => 'users', 'menu' => 'Data Users', 'category' => 'Data Utama'],
    ['key' => 'gejala', 'menu' => 'Data Gejala', 'category' => 'Basis Perhitungan'],
    ['key' => 'hipotesis', 'menu' => 'Data Hipotesis', 'category' => 'Basis Perhitungan'],
    ['key' => 'standar', 'menu' => 'Standar Antropometri', 'category' => 'Basis Perhitungan'],
    ['key' => 'rulebased', 'menu' => 'Rule Based', 'category' => 'Basis Perhitungan'],
    ['key' => 'prior', 'menu' => 'Prior Theorema Bayes', 'category' => 'Basis Perhitungan'],
    ['key' => 'likelihood', 'menu' => 'Probabilitas Antropometri', 'category' => 'Basis Perhitungan'],
    ['key' => 'nilaiprobabilitas', 'menu' => 'Probabilitas Gejala', 'category' => 'Basis Perhitungan'],
    ['key' => 'settings', 'menu' => 'Pengaturan Profil', 'category' => 'Akun'],
];
$rolePermissionMatrix = $rolePermissionMatrix ?? [
    'admin1' => [
        'dashboard' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'anak' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => true],
        'statusgizi' => ['lihat' => true, 'tambah' => true, 'edit' => false, 'hapus' => false],
        'hasildiagnosa' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => true],
        'users' => ['lihat' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
        'gejala' => ['lihat' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
        'hipotesis' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'standar' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => false],
        'rulebased' => ['lihat' => true, 'tambah' => true, 'edit' => true, 'hapus' => true],
        'prior' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => false],
        'likelihood' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => false],
        'nilaiprobabilitas' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'settings' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => false],
    ],
    'admin2' => [
        'dashboard' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'anak' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => true],
        'statusgizi' => ['lihat' => true, 'tambah' => true, 'edit' => false, 'hapus' => false],
        'hasildiagnosa' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'users' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'gejala' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'hipotesis' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'standar' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'rulebased' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'prior' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'likelihood' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'nilaiprobabilitas' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'settings' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => false],
    ],
    'admin3' => [
        'dashboard' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'anak' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'statusgizi' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'hasildiagnosa' => ['lihat' => true, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'users' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'gejala' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'hipotesis' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'standar' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'rulebased' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'prior' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'likelihood' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'nilaiprobabilitas' => ['lihat' => false, 'tambah' => false, 'edit' => false, 'hapus' => false],
        'settings' => ['lihat' => true, 'tambah' => false, 'edit' => true, 'hapus' => false],
    ],
];
$normalizeRole = static function ($role): string {
    return match (strtolower(trim((string) $role))) {
        'admin2' => 'admin2',
        'admin3', '1', 'user' => 'admin3',
        default => 'admin1',
    };
};
$renderPermissionTable = static function (string $role, array $rows, array $matrix): void {
    $actions = $GLOBALS['permissionActionsForView'] ?? ['lihat' => 'Lihat', 'tambah' => 'Tambah', 'edit' => 'Edit', 'hapus' => 'Hapus'];
    $supportedActions = $GLOBALS['supportedPermissionActionsForView'] ?? [];
    ?>
    <div class="table-responsive admin-access-table-wrap">
        <table class="table admin-access-table" data-access-table>
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Kategori</th>
                    <?php foreach ($actions as $label): ?>
                        <th><?= esc($label); ?></th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <span class="admin-access-menu">
                                <i class="ti-check-box"></i>
                                <?= esc($row['menu']); ?>
                            </span>
                        </td>
                        <td><?= esc($row['category']); ?></td>
                        <?php foreach (array_keys($actions) as $action): ?>
                            <?php
                                $isSupported = !empty($supportedActions[$row['key']][$action]) || in_array($action, $row['supported_actions'] ?? [], true);
                                $checked = $isSupported && !empty($matrix[$role][$row['key']][$action]);
                            ?>
                            <td>
                                <?php if ($isSupported): ?>
                                    <input type="checkbox"
                                        class="admin-access-check"
                                        name="permissions[<?= esc($row['key'], 'attr'); ?>][<?= esc($action, 'attr'); ?>]"
                                        value="1"
                                        data-menu="<?= esc($row['key'], 'attr'); ?>"
                                        data-action="<?= esc($action, 'attr'); ?>"
                                        title="<?= esc($actions[$action], 'attr'); ?>"
                                        <?= $checked ? 'checked' : ''; ?>>
                                <?php else: ?>
                                    <span class="admin-access-unavailable" aria-label="Aksi ini tidak tersedia"></span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
};
$GLOBALS['permissionActionsForView'] = $permissionActions ?? ['lihat' => 'Lihat', 'tambah' => 'Tambah', 'edit' => 'Edit', 'hapus' => 'Hapus'];
$GLOBALS['supportedPermissionActionsForView'] = $supportedPermissionActions ?? [];
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
                        <div class="admin-access-card">
                            <div class="admin-access-head">
                                <div>
                                    <h5>Hak Akses Admin</h5>
                                    <p>Pilih role, centang akses menu dan aksi CRUD, lalu simpan mapping.</p>
                                </div>
                                <form class="admin-role-create-form" method="post" action="<?= base_url('/admin/createRole'); ?>">
                                    <?= csrf_field() ?>
                                    <label>
                                        <span>Kode Role</span>
                                        <input type="text" class="form-control form-control-sm" name="role_code" placeholder="contoh: admin4" required>
                                    </label>
                                    <label>
                                        <span>Nama Role</span>
                                        <input type="text" class="form-control form-control-sm" name="role_name" placeholder="contoh: Petugas Gizi" required>
                                    </label>
                                    <button type="submit" class="btn btn-primary btn-sm">Tambah Role</button>
                                </form>
                            </div>
                            <form method="post" action="<?= base_url('/admin/updateRoleAccess'); ?>">
                                <?= csrf_field() ?>
                                <div class="admin-access-role-control">
                                    <label>
                                        <span>Mapping Role</span>
                                        <select class="form-control form-control-sm" name="role" data-access-role>
                                            <?php foreach ($roleOptions as $roleValue => $roleLabel): ?>
                                                <option value="<?= esc($roleValue, 'attr'); ?>"><?= esc($roleLabel); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <button type="submit" class="btn btn-primary btn-sm">Simpan Mapping</button>
                                </div>
                                <?php $renderPermissionTable(array_key_first($roleOptions) ?: 'admin1', $permissionRows, $rolePermissionMatrix); ?>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover admin-data-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Id User</th>
                                        <th>Nama User</th>
                                        <th>Username</th>
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
                                                    <?= esc($user['username'] ?? '-'); ?>
                                                </td>
                                                <td>
                                                    <?= esc($user['email'] ?? '-'); ?>
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
                                                        data-username="<?= esc($user['username'] ?? '', 'attr'); ?>"
                                                        data-email="<?= esc($user['email'] ?? '', 'attr'); ?>"
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
                                            <td colspan="8">Data tidak tersedia.</td>
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
                            <label for="create-username">Username</label>
                            <input type="text" class="form-control" id="create-username" name="username" minlength="3" maxlength="50" required>
                            <small class="text-muted">Dipakai untuk login admin.</small>
                        </div>
                        <div class="form-group">
                            <label for="create-email">Email</label>
                            <input type="email" class="form-control" id="create-email" name="email">
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
                            <label for="edit-username">Username</label>
                            <input type="text" class="form-control" id="edit-username" name="username" minlength="3" maxlength="50" required>
                            <small class="text-muted">Dipakai untuk login admin.</small>
                        </div>
                        <div class="form-group">
                            <label for="edit-email">Email</label>
                            <input type="email" class="form-control" id="edit-email" name="email">
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
            var accessMatrix = <?= json_encode($rolePermissionMatrix, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); ?>;

            function updateAccessTable(scope, role) {
                var matrix = accessMatrix[role] || accessMatrix.admin3 || {};
                scope.querySelectorAll('[data-access-table] .admin-access-check').forEach(function (checkbox) {
                    var menu = checkbox.getAttribute('data-menu') || '';
                    var action = checkbox.getAttribute('data-action') || '';
                    checkbox.checked = Boolean(matrix[menu] && matrix[menu][action]);
                });
            }

            document.querySelectorAll('[data-access-role]').forEach(function (select) {
                var scope = select.closest('.modal-content') || select.closest('.admin-access-card') || document;
                select.addEventListener('change', function () {
                    updateAccessTable(scope, select.value);
                });
                updateAccessTable(scope, select.value);
            });

            $('#editUserModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');

                $('#editUserForm').attr('action', '<?= base_url('/admin/updateUser'); ?>/' + id);
                $('#edit-nama').val(button.data('nama'));
                $('#edit-username').val(button.data('username'));
                $('#edit-email').val(button.data('email'));
                $('#edit-role').val(button.data('role'));
                $('#edit-password').val('');
                $('#edit-foto').val('');
            });
        });
    </script>

    <?= $this->include('layout/dashboard/footer') ?>
