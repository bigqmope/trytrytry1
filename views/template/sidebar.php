<?php
// pastikan variabel halaman ada
$currentPage = $page ?? ($activeMenu ?? 'dashboard');
?>

<aside class="app-sidebar modern-sidebar bg-primary-blue">

  <!-- Brand -->
  <div class="sidebar-brand d-flex align-items-center">
    <div class="brand-logo-circle me-2">
      <img src="img/Logo.png" class="brand-logo-img" alt="Logo">
    </div>
    <div class="d-flex flex-column">
      <span class="brand-name text-white fw-bold">SIP Hewan</span>
      <span class="brand-subtitle text-light">Admin Panel</span>
    </div>
  </div>

  <!-- Wrapper isi sidebar -->
  <div class="sidebar-wrapper d-flex flex-column">

    <nav class="mt-2 flex-grow-1">
      <ul class="nav flex-column modern-menu">

        <!-- DASHBOARD -->
        <li class="nav-item mb-1">
          <a href="index.php?page=dashboard"
             class="nav-link modern-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-3x3-gap modern-icon"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <!-- MENU DATA (dropdown custom) -->
        <li class="nav-item mb-1 menu-group">
          <a href="#"
             class="nav-link modern-link has-dropdown <?= in_array($currentPage, ['hewan','pemilik','pelanggan','layanan']) ? 'active-dropdown' : '' ?>"
             data-target="#menuData">
            <i class="bi bi-folder modern-icon"></i>
            <span>Data</span>
            <i class="bi bi-chevron-down dropdown-arrow ms-auto"></i>
          </a>

          <div class="submenu <?= in_array($currentPage, ['hewan','pemilik','pelanggan','layanan']) ? 'show' : '' ?>"
               id="menuData">
            <a href="index.php?page=hewan"
               class="nav-link sub-link <?= $currentPage === 'hewan' ? 'active' : '' ?>">
              <i class="bi bi-dot me-1"></i> Data Hewan
            </a>
            <a href="index.php?page=pemilik"
               class="nav-link sub-link <?= $currentPage === 'pemilik' || $currentPage === 'pelanggan' ? 'active' : '' ?>">
              <i class="bi bi-dot me-1"></i> Data Pelanggan
            </a>
            <a href="index.php?page=layanan"
               class="nav-link sub-link <?= $currentPage === 'layanan' ? 'active' : '' ?>">
              <i class="bi bi-dot me-1"></i> Jenis Layanan
            </a>
          </div>
        </li>

        <!-- TRANSAKSI PENITIPAN -->
        <li class="nav-item mb-1">
          <a href="index.php?page=transaksi"
             class="nav-link modern-link <?= $currentPage === 'transaksi' ? 'active' : '' ?>">
            <i class="bi bi-receipt modern-icon"></i>
            <span>Transaksi Penitipan</span>
          </a>
        </li>

        <!-- LOGOUT -->
        <li class="nav-item mt-2">
          <a href="/index.php?page=logout" class="nav-link modern-link text-warning">
            <i class="bi bi-box-arrow-right modern-icon"></i>
            <span>Logout</span>
          </a>
        </li>

      </ul>
    </nav>

  </div>
</aside>
