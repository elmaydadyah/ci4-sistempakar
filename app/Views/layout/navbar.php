<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
  
  <!-- Logo -->
  <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
    <div class="me-3">
      <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
        <span class="icon-menu"></span>
      </button>
    </div>
    <div>
      <a class="navbar-brand brand-logo" href="<?= base_url('dashboard') ?>">
        <img src="<?= base_url('assets/skydash/assets/images/logo.svg') ?>" alt="logo" />
      </a>
    </div>
  </div>

  <!-- Menu kanan -->
  <div class="navbar-menu-wrapper d-flex align-items-top"> 
    <ul class="navbar-nav ms-auto">

      <!-- Notifikasi -->
      <li class="nav-item dropdown">
        <a class="nav-link count-indicator" id="notificationDropdown" href="#" data-bs-toggle="dropdown">
          <i class="icon-bell"></i>
          <span class="count"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end navbar-dropdown">
          <h6 class="dropdown-header">Notifications</h6>
          <a class="dropdown-item">Belum ada notifikasi</a>
        </div>
      </li>

      <!-- Profile -->
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
          <img src="<?= base_url('assets/skydash/assets/images/faces/face1.jpg') ?>" class="rounded-circle" width="30">
          <span class="ms-2">Admin</span>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
          <a class="dropdown-item" href="#">Profile</a>
          <a class="dropdown-item" href="<?= base_url('login') ?>">Logout</a>
        </div>
      </li>

    </ul>
  </div>

</nav>