<?php
$profileFoto = base_url('assets/skydash/images/faces/face28.jpg');
$profileNama = session()->get('nama') ?? 'User';

if (session()->get('user_id')) {
  $usersModel = new \App\Models\UsersModel();
  $loggedUser = $usersModel->find(session()->get('user_id'));

  if (!empty($loggedUser)) {
    $profileNama = $loggedUser['nama'] ?? $profileNama;

    if (!empty($loggedUser['foto']) && is_file(FCPATH . 'uploads/foto_users/' . $loggedUser['foto'])) {
      $profileFoto = base_url('uploads/foto_users/' . $loggedUser['foto']);
    }
  }
}
?>
<nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    <a class="navbar-brand brand-logo mr-5 d-flex align-items-center" href="<?= base_url('dashboard') ?>">
      <img src="<?= base_url('assets/images/logo/logo.png') ?>" class="mr-2" alt="Logo StuntCare" />
      <span class="dashboard-brand-text">
        <strong>StuntCare</strong>
        <small>Sistem Deteksi Dini Stunting</small>
      </span>
    </a>
    <a class="navbar-brand brand-logo-mini" href="<?= base_url('dashboard') ?>"><img
        src="<?= base_url('assets/images/logo/logo.png') ?>" alt="Logo StuntCare" /></a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
    <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
      <span class="icon-menu"></span>
    </button>
    <ul class="navbar-nav navbar-nav-right">
      <li class="nav-item nav-profile dropdown ml-auto">
        <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-end" href="#"
          data-toggle="dropdown" id="profileDropdown">
          <span class="mr-2 d-none d-md-inline">Welcome, <?= htmlspecialchars($profileNama, ENT_QUOTES, 'UTF-8'); ?></span>
          <img src="<?= $profileFoto; ?>" alt="<?= htmlspecialchars($profileNama, ENT_QUOTES, 'UTF-8'); ?>" />
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item" href="<?= base_url('logout') ?>">
            <i class="ti-power-off text-primary"></i>
            Logout
          </a>
        </div>
      </li>
    </ul>
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
      data-toggle="offcanvas">
      <span class="icon-menu"></span>
    </button>
  </div>
</nav>
