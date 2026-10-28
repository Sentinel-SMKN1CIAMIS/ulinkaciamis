@extends('admin.layouts.app')

@section('title', 'Kelola Destinasi & Fasilitas')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Kelola Destinasi &amp; Fasilitas</h1>
    <p class="page-sub">
      @if($isRegional)
        Menampilkan <strong>{{ $locations->total() }} destinasi / fasilitas</strong> terdaftar di wilayah tugas Anda (<strong>Kecamatan {{ $userWilayah }}</strong>)
      @else
        Menampilkan total <strong>{{ $locations->total() }} destinasi / fasilitas</strong> terdaftar di seluruh 27 Kecamatan Tatar Galuh Ciamis
      @endif
    </p>
  </div>
  <a href="{{ route('admin.locations.create') }}" class="btn-primary"><i class="fas fa-plus"></i> Tambah Destinasi Baru</a>
</div>

@if($isRegional)
<div class="alert alert-success" style="background:#f0fdf4;border-color:#bbf7d0;color:#166534;display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-radius:10px;margin-bottom:16px;">
  <div style="display:flex;align-items:center;gap:10px;">
    <i class="fas fa-shield-halved" style="font-size:18px;"></i>
    <span>Hak Akses Terkunci: <strong>Staff Wilayah Kecamatan {{ $userWilayah }}</strong> (Hanya mengelola data di wilayah Anda)</span>
  </div>
  <span style="font-size:11.5px;background:#dcfce7;color:#15803d;padding:3px 8px;border-radius:6px;font-weight:700;"><i class="fas fa-lock"></i> Otoritas Khusus</span>
</div>
@endif

<!-- FILTER TOOLBAR -->
<div class="filter-bar">
  <form method="GET" action="{{ route('admin.locations.index') }}" class="filter-form">
    <div class="search-filter">
      <i class="fas fa-search"></i>
      <input type="text" name="q" placeholder="Cari nama tempat, sekolah, minimarket, bengkel..." value="{{ request('q') }}">
    </div>
    <select name="kat">
      <option value="">Semua Kategori</option>
      @foreach($categories as $k)
        <option value="{{ $k->id }}" {{ request('kat') == $k->id ? 'selected' : '' }}>{{ $k->name }}</option>
      @endforeach
    </select>
    
    @if($isRegional)
      <select name="kec" disabled style="background:#f1f5f9;cursor:not-allowed;">
        <option value="{{ $userWilayah }}" selected>Kec. {{ $userWilayah }} (Terkunci)</option>
      </select>
    @else
      <select name="kec">
        <option value="">Semua Kecamatan (27)</option>
        @foreach($kecamatanList as $kc)
          <option value="{{ $kc }}" {{ request('kec') === $kc ? 'selected' : '' }}>Kec. {{ $kc }}</option>
        @endforeach
      </select>
    @endif

    <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Terapkan Filter</button>
    @if(request('q') || request('kat') || (! $isRegional && request('kec')))
      <a href="{{ route('admin.locations.index') }}" class="btn-ghost-sm"><i class="fas fa-rotate-left"></i> Reset</a>
    @endif
  </form>
</div>

<!-- TABLE CARD -->
<div class="table-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width:40px;">No</th>
          <th style="width:50px;">Foto</th>
          <th>Nama Tempat &amp; Alamat</th>
          <th>Kecamatan</th>
          <th>Kategori</th>
          <th>Fasilitas</th>
          <th>Status</th>
          <th>Penginput</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
      @forelse($locations as $i => $r)
      <tr>
        <td style="color:var(--text-muted);font-weight:600;font-size:12px;">{{ $locations->firstItem() + $i }}</td>
        <td>
          <img src="{{ $r->image_url ?: asset('assets/images/alun_alun_ciamis.jpg') }}" 
               alt="{{ $r->name }}" 
               class="dest-preview-thumb" 
               onerror="this.src='{{ asset('assets/images/alun_alun_ciamis.jpg') }}'" />
        </td>
        <td>
          <div style="font-weight:700;color:var(--text-main);font-size:13.5px;">{{ $r->name }}</div>
          @if($r->address)
            <div style="font-size:12px;color:var(--text-muted);margin-top:2px;">
              {{ \Illuminate\Support\Str::limit($r->address, 52) }}
            </div>
          @endif
        </td>
        <td>
          <span class="badge" style="background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;">
            Kec. {{ $r->kecamatan ?: 'Ciamis' }}
          </span>
        </td>
        <td><span class="badge-cat">{{ $r->category->name ?? 'Wisata' }}</span></td>
        <td>
          @if($r->has_wifi)
            <span class="badge-wifi"><i class="fas fa-wifi"></i> WiFi</span>
          @else
            <span style="color:var(--text-light);font-size:12px;">-</span>
          @endif
        </td>
        <td>
          @if($r->is_active)
            <span class="badge-status active"><i class="fas fa-circle" style="font-size:7px;"></i> Aktif</span>
          @else
            <span class="badge-status inactive"><i class="fas fa-circle" style="font-size:7px;"></i> Nonaktif</span>
          @endif
        </td>
        <td>
          <span style="font-size:12px;color:var(--text-muted);">
            {{ $r->creator->name ?? 'Admin' }}
          </span>
        </td>
        <td style="text-align:right;">
          <div class="tbl-actions" style="justify-content:flex-end;">
            <a href="{{ route('admin.locations.edit', $r->id) }}" class="tbl-btn edit" title="Edit Data"><i class="fas fa-pen-to-square"></i></a>
            <a href="{{ url('/?q=' . urlencode($r->name)) }}" target="_blank" class="tbl-btn view" title="Lihat di Peta"><i class="fas fa-map-pin"></i></a>
            <form action="{{ route('admin.locations.destroy', $r->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus: {{ addslashes($r->name) }}?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="tbl-btn del" title="Hapus Destinasi" style="border:none;cursor:pointer;"><i class="fas fa-trash-can"></i></button>
            </form>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="9" style="text-align:center;padding:48px 20px;color:var(--text-muted);">
          <i class="fas fa-folder-open" style="font-size:32px;color:var(--text-light);margin-bottom:8px;display:block;"></i>
          Tidak ada data destinasi yang ditemukan di wilayah ini.
        </td>
      </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  <!-- PAGINATION -->
  @if($locations->hasPages())
  <div class="pagination">
    <span style="margin-right:auto;font-size:12.5px;color:var(--text-muted);">
      Halaman <strong>{{ $locations->currentPage() }}</strong> dari <strong>{{ $locations->lastPage() }}</strong> (Total {{ $locations->total() }} data)
    </span>
    {{ $locations->links() }}
  </div>
  @endif
</div>
@endsection
