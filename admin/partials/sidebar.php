<?php
$current_page = basename($_SERVER['PHP_SELF']);
function navActive($pages, $current)
{
    return in_array($current, (array) $pages) ? 'active' : '';
}
$disp_name = $_SESSION['admin_user']['nama_lengkap'] ?? $_SESSION['admin_nama'] ?? 'Administrator';
$disp_role = $_SESSION['admin_user']['role_label'] ?? (! empty($_SESSION['admin_wilayah']) && $_SESSION['admin_wilayah'] !== 'Seluruh Kabupaten Ciamis' ? 'Staff Kec. '.$_SESSION['admin_wilayah'] : 'Super Administrator');
$avatar_initial = strtoupper(substr($disp_name, 0, 1));
?>
<aside class="admin-sidebar">
  <div class="sidebar-brand">
    <div style="height:36px; min-width:36px; background:#ffffff; border-radius:8px; display:flex; align-items:center; justify-content:center; padding:3px 6px; box-shadow:0 1px 3px rgba(0,0,0,0.2); flex-shrink:0;">
      <img src="/ulinkaciamis/public/images/ulinkaCiamis.webp" alt="UlinKa Ciamis" style="height:26px; width:auto; max-width:65px; object-fit:contain; display:block;" />
    </div>
    <div class="sidebar-brand-text">
      <span class="sidebar-brand-name">UlinKa Ciamis</span>
      <span class="sidebar-brand-sub">Portal Administrator</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-group-label">Menu Utama</div>
    <a href="/ulinkaciamis/admin/dashboard.php" class="nav-item <?= navActive('dashboard.php', $current_page) ?>">
      <i class="fas fa-chart-pie"></i>
      <span>Dashboard</span>
    </a>

    <div class="nav-group-label" style="margin-top:14px">Manajemen Destinasi</div>
    <a href="/ulinkaciamis/admin/kelola.php" class="nav-item <?= navActive(['kelola.php', 'edit.php'], $current_page) ?>">
      <i class="fas fa-map-location-dot"></i>
      <span>Data Destinasi</span>
    </a>
    <a href="/ulinkaciamis/admin/tambah.php" class="nav-item <?= navActive('tambah.php', $current_page) ?>">
      <i class="fas fa-circle-plus"></i>
      <span>Tambah Destinasi</span>
    </a>
    <a href="/ulinkaciamis/admin/kategori.php" class="nav-item <?= navActive('kategori.php', $current_page) ?>">
      <i class="fas fa-tags"></i>
      <span>Kategori Wisata</span>
    </a>

    <div class="nav-group-label" style="margin-top:14px">Sistem &amp; Tautan</div>
    <a href="/ulinkaciamis/" class="nav-item" target="_blank">
      <i class="fas fa-arrow-up-right-from-square"></i>
      <span>Lihat Peta Publik</span>
    </a>
    <a href="/ulinkaciamis/admin/logout.php" class="nav-item text-danger" onclick="return confirm('Apakah Anda yakin ingin keluar?')">
      <i class="fas fa-arrow-right-from-bracket"></i>
      <span>Keluar Sesi</span>
    </a>
  </nav>

  <div class="sidebar-user">
    <div class="user-avatar-circle">
      <?= $avatar_initial ?>
    </div>
    <div class="user-info">
      <div class="user-name"><?= htmlspecialchars($disp_name) ?></div>
      <div class="user-role"><?= htmlspecialchars($disp_role) ?></div>
    </div>
  </div>
</aside>
