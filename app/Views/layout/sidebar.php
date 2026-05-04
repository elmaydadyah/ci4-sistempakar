<!-- partial -->
<div class="container-fluid page-body-wrapper">
  <!-- partial:partials/_sidebar.html -->
  <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
          <i class="icon-grid menu-icon"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
          <i class="icon-layout menu-icon"></i>
          <span class="menu-title">Konfigurasi</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="ui-basic">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"> <a class="nav-link" href="<?= base_url('adminusers') ?>">Data Users</a></li>
            <li class="nav-item"> <a class="nav-link" href="<?= base_url('admingejala') ?>">Data Gejala</a></li>
            <li class="nav-item"> <a class="nav-link" href="<?= base_url('adminkasusgejala') ?>">Data Kasus Gejala</a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="collapse" href="#form-elements" aria-expanded="false"
          aria-controls="form-elements">
          <i class="icon-columns menu-icon"></i>
          <span class="menu-title">Konsultasi</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse" id="form-elements">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link" href="<?= base_url('adminkonsultasi') ?>">Konsultasi</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </nav>