<?= $this->include('layout/dashboard/header') ?>
<?= $this->include('layout/dashboard/navbar') ?>
<?= $this->include('layout/dashboard/sidebar') ?>

<?php
$fotoProfil = base_url('assets/skydash/images/faces/face28.jpg');
if (!empty($user['foto']) && is_file(FCPATH . 'uploads/foto_users/' . $user['foto'])) {
    $fotoProfil = base_url('uploads/foto_users/' . $user['foto']);
}
?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="row">
            <div class="col-12 grid-margin stretch-card">
                <div class="card admin-settings-card">
                    <div class="card-body">
                        <div class="admin-table-toolbar">
                            <div>
                                <h4 class="card-title mb-1">Setting Profil</h4>
                                <p class="text-muted mb-0">Ubah foto profil, nama, username, email, dan password admin.</p>
                            </div>
                        </div>

                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= esc(session()->getFlashdata('success')); ?></div>
                        <?php endif; ?>
                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')); ?></div>
                        <?php endif; ?>

                        <form method="post" action="<?= base_url('adminsettings'); ?>" enctype="multipart/form-data" class="admin-settings-form">
                            <?= csrf_field() ?>
                            <div class="admin-settings-photo">
                                <img src="<?= esc($fotoProfil, 'attr'); ?>" alt="<?= esc($user['nama'] ?? 'Admin', 'attr'); ?>">
                                <div>
                                    <strong><?= esc($user['nama'] ?? 'Admin'); ?></strong>
                                    <span><?= esc($user['username'] ?? '-'); ?></span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label for="settings-nama">Nama Admin</label>
                                    <input type="text" class="form-control" id="settings-nama" name="nama" value="<?= esc($user['nama'] ?? '', 'attr'); ?>" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="settings-username">Username</label>
                                    <input type="text" class="form-control" id="settings-username" name="username" value="<?= esc($user['username'] ?? '', 'attr'); ?>" minlength="3" maxlength="50" required>
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="settings-email">Email</label>
                                    <input type="email" class="form-control" id="settings-email" name="email" value="<?= esc($user['email'] ?? '', 'attr'); ?>">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="settings-password">Password Baru</label>
                                    <input type="password" class="form-control" id="settings-password" name="password" placeholder="Kosongkan jika tidak diganti">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="settings-foto">Foto Profil</label>
                                    <input type="file" class="form-control" id="settings-foto" name="foto" accept="image/png,image/jpeg">
                                </div>
                            </div>

                            <div class="admin-settings-actions">
                                <a class="btn btn-light" href="<?= base_url('dashboard'); ?>">Batal</a>
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?= $this->include('layout/dashboard/footer') ?>
