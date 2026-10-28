@props(['title' => 'Portal Administrator'])
<div class="admin-topbar">
  <div class="topbar-left">
    <button class="topbar-menu-btn" id="mobileSidebarBtn" aria-label="Toggle Menu">
      <i class="fas fa-bars"></i>
    </button>
    <div class="topbar-breadcrumb">
      <span>Portal Administrator</span>
      <i class="fas fa-chevron-right" style="font-size:10px;color:var(--text-light)"></i>
      <span class="active">{{ $title }}</span>
    </div>
  </div>

  <div class="topbar-right">
    <div class="topbar-badge-env">
      <span class="status-dot"></span>
      <span>Sistem Online • Ciamis</span>
    </div>
    <a href="{{ url('/') }}" target="_blank" class="topbar-link">
      <i class="fas fa-compass"></i>
      <span>Buka Peta Publik</span>
    </a>
  </div>
</div>
<script>
document.getElementById('mobileSidebarBtn')?.addEventListener('click', () => {
  document.querySelector('.admin-sidebar')?.classList.toggle('open');
});
</script>
