<div class="container-fluid page-body-wrapper">
  <?php
  $currentPath = trim(uri_string(), '/');
  $isDashboard = $currentPath === 'dashboard';
  $isDataMaster = in_array($currentPath, ['adminusers', 'admingejala', 'adminpenyakit', 'adminanak', 'adminsolusi'], true);
  $isBasisAturan = in_array($currentPath, ['adminstatusgizi', 'admincf'], true);
  $isDiagnosa = in_array($currentPath, ['adminkonsultasi', 'adminhasildiagnosa'], true);
  ?>
  <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item <?= $isDashboard ? 'active' : ''; ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
          <i class="icon-grid menu-icon"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>

      <li class="nav-item <?= $isDataMaster ? 'active' : ''; ?>">
        <a class="nav-link" data-toggle="collapse" href="#tables" aria-expanded="<?= $isDataMaster ? 'true' : 'false'; ?>" aria-controls="tables">
          <i class="icon-grid-2 menu-icon"></i>
          <span class="menu-title">Data Master</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse <?= $isDataMaster ? 'show' : ''; ?>" id="tables">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminanak' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminanak') ?>">Data Anak</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminusers' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminusers') ?>">Data Users</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'admingejala' ? 'active-submenu' : ''; ?>" href="<?= base_url('admingejala') ?>">Data Gejala</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminpenyakit' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminpenyakit') ?>">Data Penyakit</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item <?= $isBasisAturan ? 'active' : ''; ?>">
        <a class="nav-link" data-toggle="collapse" href="#basis-aturan" aria-expanded="<?= $isBasisAturan ? 'true' : 'false'; ?>" aria-controls="basis-aturan">
          <i class="icon-briefcase menu-icon"></i>
          <span class="menu-title">Basis Aturan NB + CF</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse <?= $isBasisAturan ? 'show' : ''; ?>" id="basis-aturan">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminstatusgizi' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminstatusgizi') ?>">Data Latih NB</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'admincf' ? 'active-submenu' : ''; ?>" href="<?= base_url('admincf') ?>">Certainty Factor</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item <?= $isDiagnosa ? 'active' : ''; ?>">
        <a class="nav-link" data-toggle="collapse" href="#diagnosa" aria-expanded="<?= $isDiagnosa ? 'true' : 'false'; ?>" aria-controls="diagnosa">
          <i class="icon-columns menu-icon"></i>
          <span class="menu-title">Diagnosa</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse <?= $isDiagnosa ? 'show' : ''; ?>" id="diagnosa">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminkonsultasi' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminkonsultasi') ?>">Proses Diagnosa</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminhasildiagnosa' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminhasildiagnosa') ?>">Hasil Diagnosa</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </nav>
