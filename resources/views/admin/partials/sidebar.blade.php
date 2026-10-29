@php
    $user = auth()->user();
    $dispName = $user->name ?? 'Administrator';
    $dispRole = $user->role_label ?? ($user->isRegional() ? 'Staff Kec. ' . $user->wilayah : 'Super Administrator');
    $avatarInitial = strtoupper(substr($dispName, 0, 1));
@endphp
<aside class="admin-sidebar">
  <div class="sidebar-brand">
    <div style="background:#ffffff; border-radius:10px; display:flex; align-items:center; justify-content:center; padding:6px 10px; box-shadow:0 2px 5px rgba(0,0,0,0.2); flex-shrink:0;">
      <img src="{{ asset('images/ulinkaCiamis.webp') }}" alt="UlinKa Ciamis" style="height:38px; width:auto; max-width:120px; object-fit:contain; display:block;" />
    </div>
    <div class="sidebar-brand-text">
      <span class="sidebar-brand-name">UlinKa Ciamis</span>
      <span class="sidebar-brand-sub">Portal Administrator</span>
    </div>
  </div>

  <nav class="sidebar-nav">
    <div class="nav-group-label">Menu Utama</div>
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
      <i class="fas fa-chart-pie"></i>
      <span>Dashboard</span>
    </a>

    <div class="nav-group-label" style="margin-top:14px">Manajemen Destinasi</div>
    <a href="{{ route('admin.locations.index') }}" class="nav-item {{ request()->routeIs('admin.locations.index', 'admin.locations.edit') ? 'active' : '' }}">
      <i class="fas fa-map-location-dot"></i>
      <span>Data Destinasi</span>
    </a>
    <a href="{{ route('admin.locations.create') }}" class="nav-item {{ request()->routeIs('admin.locations.create') ? 'active' : '' }}">
      <i class="fas fa-circle-plus"></i>
      <span>Tambah Destinasi</span>
    </a>
    <a href="{{ route('admin.categories.index') }}" class="nav-item {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
      <i class="fas fa-tags"></i>
      <span>Kategori Wisata</span>
    </a>

    <div class="nav-group-label" style="margin-top:14px">Sistem &amp; Tautan</div>
    <a href="{{ url('/') }}" class="nav-item" target="_blank">
      <i class="fas fa-arrow-up-right-from-square"></i>
      <span>Lihat Peta Publik</span>
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
      @csrf
    </form>
    <a href="{{ route('logout') }}" class="nav-item text-danger" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin keluar?')) { document.getElementById('logout-form').submit(); }">
      <i class="fas fa-arrow-right-from-bracket"></i>
      <span>Keluar Sesi</span>
    </a>
  </nav>

  <div class="sidebar-user">
    <div class="user-avatar-circle">
      {{ $avatarInitial }}
    </div>
    <div class="user-info">
      <div class="user-name">{{ $dispName }}</div>
      <div class="user-role">{{ $dispRole }}</div>
    </div>
  </div>
</aside>
