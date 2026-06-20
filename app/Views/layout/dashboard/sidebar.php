<div class="container-fluid page-body-wrapper">
  <?php
  $roleAccess = new \App\Libraries\RoleAccess();
  $currentPath = trim(uri_string(), '/');
  $role = $roleAccess->normalizeRole((string) (session()->get('role') ?? 'admin1'));
  $menuMap = [
    'admingejala' => 'gejala',
    'adminhipotesis' => 'hipotesis',
    'adminusers' => 'users',
    'adminanak' => 'anak',
    'adminstatusgizi' => 'statusgizi',
    'adminstandar' => 'standar',
    'adminrulebased' => 'rulebased',
    'adminprior' => 'prior',
    'adminlikelihood' => 'likelihood',
    'adminnilaiprobabilitas' => 'nilaiprobabilitas',
    'adminhasildiagnosa' => 'hasildiagnosa',
  ];
  $canSee = static fn (string $menu): bool => $roleAccess->hasPermission($role, $menuMap[$menu] ?? $menu, 'lihat');
  $isDashboard = $currentPath === 'dashboard';
  $isDataUtama = in_array($currentPath, ['adminanak', 'adminstatusgizi', 'adminhasildiagnosa', 'adminusers'], true);
  $isPerhitungan = in_array($currentPath, ['admingejala', 'adminhipotesis', 'adminstandar', 'adminrulebased', 'adminprior', 'adminlikelihood', 'adminnilaiprobabilitas'], true);
  ?>
  <nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
      <li class="nav-item <?= $isDashboard ? 'active' : ''; ?>">
        <a class="nav-link" href="<?= base_url('dashboard') ?>">
          <i class="icon-grid menu-icon"></i>
          <span class="menu-title">Dashboard</span>
        </a>
      </li>

      <?php if ($canSee('adminanak') || $canSee('adminstatusgizi') || $canSee('adminhasildiagnosa') || $canSee('adminusers')): ?>
      <li class="nav-item <?= $isDataUtama ? 'active' : ''; ?>">
        <a class="nav-link" data-toggle="collapse" href="#data-utama" aria-expanded="<?= $isDataUtama ? 'true' : 'false'; ?>" aria-controls="data-utama">
          <i class="icon-grid-2 menu-icon"></i>
          <span class="menu-title">Data Utama</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse <?= $isDataUtama ? 'show' : ''; ?>" id="data-utama" data-parent="#sidebar">
          <ul class="nav flex-column sub-menu">
            <?php if ($canSee('adminanak')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminanak' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminanak') ?>"><i class="ti-user submenu-icon"></i>Data Anak</a></li><?php endif; ?>
            <?php if ($canSee('adminstatusgizi')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminstatusgizi' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminstatusgizi') ?>"><i class="ti-clipboard submenu-icon"></i>Data Terdahulu</a></li><?php endif; ?>
            <?php if ($canSee('adminhasildiagnosa')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminhasildiagnosa' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminhasildiagnosa') ?>"><i class="ti-pulse submenu-icon"></i>Hasil Diagnosa</a></li><?php endif; ?>
            <?php if ($canSee('adminusers')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminusers' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminusers') ?>"><i class="ti-id-badge submenu-icon"></i>Data Users</a></li><?php endif; ?>
          </ul>
        </div>
      </li>
      <?php endif; ?>

      <?php if ($canSee('admingejala') || $canSee('adminhipotesis') || $canSee('adminstandar') || $canSee('adminrulebased') || $canSee('adminprior') || $canSee('adminlikelihood') || $canSee('adminnilaiprobabilitas')): ?>
      <li class="nav-item <?= $isPerhitungan ? 'active' : ''; ?>">
        <a class="nav-link" data-toggle="collapse" href="#basis-perhitungan" aria-expanded="<?= $isPerhitungan ? 'true' : 'false'; ?>" aria-controls="basis-perhitungan">
          <i class="icon-briefcase menu-icon"></i>
          <span class="menu-title">Basis Perhitungan</span>
          <i class="menu-arrow"></i>
        </a>
        <div class="collapse <?= $isPerhitungan ? 'show' : ''; ?>" id="basis-perhitungan" data-parent="#sidebar">
          <ul class="nav flex-column sub-menu">
            <?php if ($canSee('admingejala')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'admingejala' ? 'active-submenu' : ''; ?>" href="<?= base_url('admingejala') ?>"><i class="ti-pulse submenu-icon"></i>Data Gejala</a></li><?php endif; ?>
            <?php if ($canSee('adminhipotesis')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminhipotesis' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminhipotesis') ?>"><i class="ti-light-bulb submenu-icon"></i>Data Hipotesis</a></li><?php endif; ?>
            <?php if ($canSee('adminstandar')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminstandar' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminstandar') ?>"><i class="ti-ruler-alt-2 submenu-icon"></i>Standar Antropometri</a></li><?php endif; ?>
            <?php if ($canSee('adminrulebased')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminrulebased' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminrulebased') ?>"><i class="ti-control-shuffle submenu-icon"></i>Rule Based</a></li><?php endif; ?>
            <?php if ($canSee('adminprior')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminprior' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminprior') ?>"><i class="ti-pin-alt submenu-icon"></i>Prior</a></li><?php endif; ?>
            <?php if ($canSee('adminlikelihood')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminlikelihood' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminlikelihood') ?>"><i class="ti-stats-up submenu-icon"></i>Probabilitas Antropometri</a></li><?php endif; ?>
            <?php if ($canSee('adminnilaiprobabilitas')): ?><li class="nav-item"><a class="nav-link <?= $currentPath === 'adminnilaiprobabilitas' ? 'active-submenu' : ''; ?>" href="<?= base_url('adminnilaiprobabilitas') ?>"><i class="ti-bar-chart submenu-icon"></i>Probabilitas Gejala</a></li><?php endif; ?>
          </ul>
        </div>
      </li>
      <?php endif; ?>
    </ul>

    <div class="sidebar-others">
      <span class="sidebar-others-title">Others</span>
      <a class="sidebar-others-link <?= $currentPath === 'adminsettings' ? 'is-active' : ''; ?>" href="<?= base_url('adminsettings') ?>">
        <i class="ti-settings"></i>
        <span>Setting</span>
      </a>
      <a class="sidebar-others-link" href="<?= base_url('logout') ?>" data-confirm-title="Konfirmasi Logout" data-confirm-message="Apakah Anda yakin ingin keluar?">
        <i class="ti-shift-right"></i>
        <span>Log out</span>
      </a>
    </div>
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
