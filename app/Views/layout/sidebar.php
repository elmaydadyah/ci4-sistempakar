<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('dashboard') ?>">
        <i class="mdi mdi-grid-large menu-icon"></i>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <!-- Users -->
    <li class="nav-item">
      <a class="nav-link" href="<?= base_url('users') ?>">
        <i class="mdi mdi-account menu-icon"></i>
        <span class="menu-title">Data User</span>
      </a>
    </li>

    <!-- Dropdown Menu -->
    <li class="nav-item">
      <a class="nav-link" data-bs-toggle="collapse" href="#menu1">
        <i class="mdi mdi-folder menu-icon"></i>
        <span class="menu-title">Menu Lain</span>
        <i class="menu-arrow"></i>
      </a>
      <div class="collapse" id="menu1">
        <ul class="nav flex-column sub-menu">
          <li class="nav-item">
            <a class="nav-link" href="#">Sub Menu 1</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Sub Menu 2</a>
          </li>
        </ul>
      </div>
    </li>

  </ul>
</nav>