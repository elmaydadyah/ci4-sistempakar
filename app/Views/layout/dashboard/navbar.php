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

$profileNamaEsc = htmlspecialchars((string) $profileNama, ENT_QUOTES, 'UTF-8');
?>
<nav class="navbar dashboard-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
    <a class="dashboard-brand brand" href="<?= base_url('dashboard') ?>" aria-label="Puskesmas Cileungsi">
      <img class="dashboard-brand-logo landing-brand-logo" src="<?= base_url('assets/images/logo/logo_puskesmas.png') ?>" alt="Logo Puskesmas" width="52" height="52">
      <span class="dashboard-brand-text brand-text">
        <strong>Puskesmas Cileungsi</strong>
        <small>Deteksi Dini Risiko Stunting</small>
      </span>
    </a>
  </div>
  <div class="navbar-menu-wrapper d-flex align-items-center">
    <div class="dashboard-navbar-left">
      <button class="navbar-toggler dashboard-navbar-icon dashboard-navbar-minimize align-self-center" type="button" data-toggle="minimize" aria-label="Tutup sidebar">
        <span class="icon-menu"></span>
      </button>
    </div>

    <ul class="navbar-nav dashboard-navbar-profile">
      <li class="nav-item nav-profile dropdown">
        <a class="nav-link dashboard-welcome-link dropdown-toggle d-flex align-items-center" href="#"
          data-toggle="dropdown" id="profileDropdown" aria-label="Menu profil <?= $profileNamaEsc; ?>">
          <img src="<?= $profileFoto; ?>" alt="<?= $profileNamaEsc; ?>" />
          <span class="dashboard-welcome-copy">
            <strong>Welcome, <?= $profileNamaEsc; ?></strong>
            <small>Dashboard Admin</small>
          </span>
        </a>
        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
          <a class="dropdown-item" href="<?= base_url('logout') ?>" data-confirm-title="Konfirmasi Logout" data-confirm-message="Apakah Anda yakin ingin keluar?">
            <i class="ti-power-off text-primary"></i>
            Logout
          </a>
        </div>
      </li>
    </ul>

    <button class="navbar-toggler dashboard-navbar-icon navbar-toggler-right d-lg-none align-self-center" type="button"
      data-toggle="offcanvas">
      <span class="icon-menu"></span>
    </button>
  </div>
</nav>
