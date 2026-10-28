@extends('admin.layouts.app')

@section('title', 'Dashboard Ringkasan')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard Administrator</h1>
    <p class="page-sub">
      Selamat datang kembali, <strong>{{ auth()->user()->name ?? 'Pengurus' }}</strong>.
      @if($isRegional)
        Anda mengelola wilayah <strong>Kecamatan {{ $userWilayah }}</strong>.
      @else
        Anda mengelola seluruh <strong>27 Kecamatan</strong> di Kabupaten Ciamis.
      @endif
    </p>
  </div>
  <div style="display:flex;align-items:center;gap:10px;">
    <a href="{{ route('admin.locations.index') }}" class="btn-secondary"><i class="fas fa-list-check"></i> Kelola Data</a>
    <a href="{{ route('admin.locations.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Tambah Destinasi</a>
  </div>
</div>

<!-- STATS CARDS -->
<div class="stats-grid">
  <div class="stat-card stat-purple">
    <div class="stat-icon"><i class="fas fa-map-location-dot"></i></div>
    <div class="stat-info">
      <div class="stat-value">{{ $totalAktif }}</div>
      <div class="stat-label">Destinasi Aktif {{ $isRegional ? "(Kec. $userWilayah)" : "Ciamis" }}</div>
    </div>
  </div>
  <div class="stat-card stat-emerald">
    <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
    <div class="stat-info">
      <div class="stat-value">{{ $totalAll }}</div>
      <div class="stat-label">Total Terdaftar {{ $isRegional ? "(Wilayah Anda)" : "Se-Kabupaten" }}</div>
    </div>
  </div>
  <div class="stat-card stat-amber">
    <div class="stat-icon"><i class="fas fa-tags"></i></div>
    <div class="stat-info">
      <div class="stat-value">{{ $totalKategori }}</div>
      <div class="stat-label">{{ $isRegional ? "Kategori Aktif di Wilayah" : "Kategori Direktori" }}</div>
    </div>
  </div>
  <div class="stat-card stat-blue">
    <div class="stat-icon"><i class="fas fa-shield-halved"></i></div>
    <div class="stat-info">
      <div class="stat-value" style="font-size:18px;line-height:28px;">
        {{ $isRegional ? "Kec. $userWilayah" : "27 Kec." }}
      </div>
      <div class="stat-label">Cakupan Wilayah Tugas</div>
    </div>
  </div>
</div>

<!-- MAIN TWO-COLUMN DASHBOARD CONTENT -->
<div style="display:grid;grid-template-columns:1fr 340px;gap:24px;margin-top:24px;">

  <!-- RECENT TABLE -->
  <div class="table-card">
    <div style="padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between;">
      <h3 style="font-size:15px;font-weight:700;color:var(--text-main);margin:0;">
        <i class="fas fa-clock-rotate-left text-muted" style="margin-right:6px;"></i> 
        Destinasi &amp; Fasilitas Terbaru {{ $isRegional ? "(Kec. $userWilayah)" : "" }}
      </h3>
      <a href="{{ route('admin.locations.index') }}" style="font-size:12.5px;color:var(--primary);font-weight:600;text-decoration:none;">Lihat Semua &rarr;</a>
    </div>

    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:40px;">Foto</th>
            <th>Nama Tempat</th>
            <th>Kategori</th>
            <th>Status</th>
            <th style="text-align:right;">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($recent as $r)
          <tr>
            <td>
              <img src="{{ $r->image_url ?: asset('assets/images/alun_alun_ciamis.jpg') }}" 
                   alt="{{ $r->name }}" 
                   class="dest-preview-thumb" 
                   onerror="this.src='{{ asset('assets/images/alun_alun_ciamis.jpg') }}'" />
            </td>
            <td>
              <div style="font-weight:700;color:var(--text-main);font-size:13.5px;">{{ $r->name }}</div>
              <div style="font-size:11.5px;color:var(--text-muted);">Kec. {{ $r->kecamatan }}</div>
            </td>
            <td><span class="badge-cat">{{ $r->category->name ?? 'Wisata' }}</span></td>
            <td>
              @if($r->is_active)
                <span class="badge-status active"><i class="fas fa-circle" style="font-size:7px;"></i> Aktif</span>
              @else
                <span class="badge-status inactive"><i class="fas fa-circle" style="font-size:7px;"></i> Nonaktif</span>
              @endif
            </td>
            <td style="text-align:right;">
              <a href="{{ route('admin.locations.edit', $r->id) }}" class="tbl-btn edit" title="Edit"><i class="fas fa-pen-to-square"></i></a>
            </td>
          </tr>
          @empty
          <tr><td colspan="5" style="text-align:center;padding:32px;color:var(--text-muted);">Belum ada data destinasi di wilayah ini.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <!-- SIDE SUMMARY -->
  <div style="display:flex;flex-direction:column;gap:18px;">
    <div class="form-card" style="padding:20px;">
      <h3 style="font-size:14.5px;font-weight:700;color:var(--text-main);margin-bottom:14px;">
        <i class="fas fa-chart-pie" style="color:var(--primary);margin-right:6px;"></i> Distribusi Kategori
      </h3>
      <div style="display:flex;flex-direction:column;gap:10px;">
        @foreach($byKat as $bk)
        <div>
          <div style="display:flex;justify-content:space-between;font-size:12.5px;font-weight:600;margin-bottom:4px;">
            <span style="color:var(--text-body);">{{ $bk->name }}</span>
            <span style="color:var(--text-muted);font-weight:700;">{{ $bk->locations_count }}</span>
          </div>
          <div style="height:6px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
            <div style="height:100%;width:{{ min(100, ($bk->locations_count / max(1, $totalAktif)) * 100) }}%;background:{{ $bk->icon_color ?: 'var(--primary)' }};border-radius:3px;"></div>
          </div>
        </div>
        @endforeach
      </div>
    </div>

    <!-- QUICK ACTION CARD -->
    <div class="form-card" style="padding:20px;background:#fdf2fa;border-color:rgba(158,11,132,0.2);">
      <div style="font-weight:800;font-size:14px;color:#9e0b84;margin-bottom:6px;">
        <i class="fas fa-map-pin"></i> Petunjuk Petugas Wilayah
      </div>
      <p style="font-size:12px;color:#475569;line-height:1.5;margin-bottom:12px;">
        Untuk menambah titik baru di wilayah Anda, buka formulir <strong>Tambah Destinasi</strong> lalu geser pin langsung pada peta untuk mendapatkan titik koordinat presisi.
      </p>
      <a href="{{ route('admin.locations.create') }}" class="btn-primary" style="width:100%;text-align:center;justify-content:center;display:flex;">
        <i class="fas fa-plus"></i> Tambah Tempat Sekarang
      </a>
    </div>
  </div>

</div>
@endsection
