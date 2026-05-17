<div class="container-fluid page-body-wrapper">
  <?php
  $currentPath = trim(uri_string(), '/');
  $isDashboard = $currentPath === 'dashboard';
  $isDataMaster = in_array($currentPath, ['admingejala', 'adminhipotesis', 'adminusers', 'adminanak', 'adminstatusgizi'], true);
  $isBasisAturan = in_array($currentPath, ['adminstandar', 'adminprior', 'adminlikelihood', 'adminnilaiprobabilitas'], true);
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
        <div class="collapse <?= $isDataMaster ? 'show' : ''; ?>" id="tables" data-parent="#sidebar">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminanak' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminanak') ?>"><i class="ti-user submenu-icon"></i>Data Anak</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'admingejala' ? 'active-submenu' : ''; ?>" href="<?= base_url('admingejala') ?>"><i class="ti-pulse submenu-icon"></i>Data Gejala</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminhipotesis' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminhipotesis') ?>"><i class="ti-light-bulb submenu-icon"></i>Data Hipotesis</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminstatusgizi' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminstatusgizi') ?>"><i class="ti-clipboard submenu-icon"></i>Data Latih</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminusers' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminusers') ?>"><i class="ti-id-badge submenu-icon"></i>Data Users</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item <?= $isBasisAturan ? 'active' : ''; ?>">
        <a class="nav-link" data-toggle="collapse" href="#basis-aturan" aria-expanded="<?= $isBasisAturan ? 'true' : 'false'; ?>" aria-controls="basis-aturan">
          <i class="icon-briefcase menu-icon"></i>
          <span class="menu-title">Referensi Perhitungan</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse <?= $isBasisAturan ? 'show' : ''; ?>" id="basis-aturan" data-parent="#sidebar">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminstandar' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminstandar') ?>"><i class="ti-ruler-alt-2 submenu-icon"></i>Standar Antropometri</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminprior' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminprior') ?>"><i class="ti-pin-alt submenu-icon"></i>Prior</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminlikelihood' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminlikelihood') ?>"><i class="ti-stats-up submenu-icon"></i>Likelihood</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminnilaiprobabilitas' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminnilaiprobabilitas') ?>"><i class="ti-bar-chart submenu-icon"></i>Nilai Probabilitas</a></li>
          </ul>
        </div>
      </li>

      <li class="nav-item <?= $isDiagnosa ? 'active' : ''; ?>">
        <a class="nav-link" data-toggle="collapse" href="#diagnosa" aria-expanded="<?= $isDiagnosa ? 'true' : 'false'; ?>" aria-controls="diagnosa">
          <i class="icon-columns menu-icon"></i>
          <span class="menu-title">Diagnosa</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse <?= $isDiagnosa ? 'show' : ''; ?>" id="diagnosa" data-parent="#sidebar">
          <ul class="nav flex-column sub-menu">
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminkonsultasi' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminkonsultasi') ?>"><i class="ti-comments submenu-icon"></i>Proses Diagnosa</a></li>
            <li class="nav-item"><a class="nav-link <?= $currentPath === 'adminhasildiagnosa' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminhasildiagnosa') ?>"><i class="ti-clipboard submenu-icon"></i>Hasil Diagnosa</a></li>
          </ul>
        </div>
      </li>
    </ul>
  </nav>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var sidebar = document.getElementById('sidebar');
      if (!sidebar) {
        return;
      }

      var rootItems = sidebar.querySelectorAll(':scope > .nav > .nav-item');
      var collapseToggles = sidebar.querySelectorAll(':scope > .nav > .nav-item > .nav-link[data-toggle="collapse"]');

      collapseToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
          rootItems.forEach(function (item) {
            item.classList.remove('active');
          });

          var item = toggle.closest('.nav-item');
          if (item) {
            item.classList.add('active');
          }
        });
      });
    });
  </script>
