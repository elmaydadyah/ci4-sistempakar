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
                        <h4 class="card-title">Tabel Users</h4>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Id User</th>
                                        <th>Nama User</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>created At</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($tb_users) && is_array($tb_users)): ?>
                                        <?php $no = 1; ?>
                                        <?php foreach ($tb_users as $user): ?>
                                            <tr>
                                                <td>
                                                    <?= $no++; ?>
                                                </td>
                                                <td>
                                                    <?= $user['id_users']; ?>
                                                </td>
                                                <td>
                                                    <?= $user['nama']; ?>
                                                </td>
                                                <td>
                                                    <?= $user['email']; ?>
                                                </td>
                                                <td>
                                                    <?= $user['role']; ?>
                                                </td>
                                                <td>
                                                    <?= $user['created_at']; ?>
                                                </td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" data-toggle="modal"
                                                        data-target="#editUserModal"
                                                        data-id="<?= $user['id_users']; ?>">Edit</button>
                                                    <a href="<?= base_url('/admin/deleteUser/' . $user['id_users']); ?>"
                                                        class="btn btn-danger btn-sm"
                                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data user ini?');">Delete</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
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

    <?= $this->include('layout/footer') ?>