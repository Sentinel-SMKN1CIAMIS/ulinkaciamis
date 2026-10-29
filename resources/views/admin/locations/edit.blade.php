@extends('admin.layouts.app')

@section('title', 'Edit Data Destinasi')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
/* Visual Category Picker Grid */
.category-picker-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 10px;
  margin-top: 8px;
}
.category-card-btn {
  background: #ffffff;
  border: 1.5px solid var(--border);
  border-radius: 12px;
  padding: 10px 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 6px;
  cursor: pointer;
  transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
  text-align: center;
  position: relative;
  user-select: none;
}
.category-card-btn:hover {
  border-color: #9e0b84;
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(158, 11, 132, 0.08);
}
.category-card-btn.active {
  border-color: #9e0b84;
  background: #fdf2fa;
  box-shadow: 0 0 0 3px rgba(158, 11, 132, 0.15);
}
.category-card-icon {
  width: 34px;
  height: 34px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 15px;
  color: #ffffff;
  transition: transform 0.2s;
}
.category-card-btn.active .category-card-icon {
  transform: scale(1.1);
}
.category-card-label {
  font-size: 11.5px;
  font-weight: 700;
  color: var(--text-main);
  line-height: 1.25;
}
.category-card-btn.active .category-card-label {
  color: #9e0b84;
}
.category-check-badge {
  position: absolute;
  top: 4px;
  right: 4px;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  background: #9e0b84;
  color: #ffffff;
  font-size: 9px;
  display: none;
  align-items: center;
  justify-content: center;
}
.category-card-btn.active .category-check-badge {
  display: flex;
}

/* Regional Lock Banner */
.regional-lock-banner {
  background: #f8fafc;
  border: 1.5px dashed #cbd5e1;
  border-radius: 12px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
}
.regional-lock-badge {
  background: #ecfdf5;
  border: 1px solid #a7f3d0;
  color: #065f46;
  font-size: 12px;
  font-weight: 800;
  padding: 4px 10px;
  border-radius: 8px;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

/* Map Interactive HUD */
.map-hud-container {
  position: relative;
  border-radius: 14px;
  overflow: hidden;
  border: 1.5px solid var(--border-strong);
  margin-top: 8px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.04);
}
.map-floating-hud {
  position: absolute;
  top: 12px;
  right: 12px;
  z-index: 1000;
  background: rgba(255, 255, 255, 0.94);
  backdrop-filter: blur(8px);
  border: 1px solid var(--border);
  padding: 7px 12px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 700;
  color: #1e293b;
  box-shadow: 0 4px 15px rgba(0,0,0,0.1);
  display: flex;
  align-items: center;
  gap: 8px;
  pointer-events: none;
}
.map-boundary-warning {
  position: absolute;
  bottom: 12px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 1000;
  background: #ef4444;
  color: #ffffff;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 800;
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
  display: none;
  align-items: center;
  gap: 8px;
  animation: pulseWarning 1.5s infinite;
}
</style>
@endpush

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Edit Destinasi &amp; Fasilitas</h1>
    <p class="page-sub">
      Mengedit data: <strong>{{ $location->name }}</strong> (Kec. {{ $location->kecamatan }})
    </p>
  </div>
  <a href="{{ route('admin.locations.index') }}" class="btn-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Daftar</a>
</div>

<div class="form-layout">
  <form method="POST" action="{{ route('admin.locations.update', $location->id) }}" enctype="multipart/form-data" class="form-card" id="editForm">
    @csrf
    @method('PUT')

    <!-- SECTION 1: INFORMASI TEMPAT & KATEGORI -->
    <div class="form-section">
      <h3 class="form-section-title"><i class="fas fa-circle-info"></i> Informasi Dasar Tempat &amp; Logo Kategori</h3>
      
      <div class="form-grid">
        <div class="form-group full">
          <label>Nama Tempat / Sekolah / Minimarket / Bengkel / Fasilitas <span class="req">*</span></label>
          <input type="text" name="nama" id="inputNama" value="{{ old('nama', $location->name) }}" required>
        </div>

        <!-- WILAYAH KECAMATAN -->
        <div class="form-group full">
          <label>Wilayah Kecamatan <span class="req">*</span></label>
          @if ($isRegional)
            <div class="regional-lock-banner">
              <div style="display:flex;align-items:center;gap:10px;">
                <i class="fas fa-shield-halved" style="font-size:22px;color:#10b981;"></i>
                <div>
                  <div style="font-size:13px;font-weight:700;color:var(--text-main);">Kecamatan {{ $userWilayah }}</div>
                  <div style="font-size:11px;color:var(--text-muted);">Akun Anda terkunci secara otomatis pada yurisdiksi tugas ini.</div>
                </div>
              </div>
              <div class="regional-lock-badge">
                <i class="fas fa-lock"></i> Terkunci (Wilayah Anda)
              </div>
            </div>
            <input type="hidden" name="kecamatan" value="{{ $userWilayah }}">
          @else
            <select name="kecamatan" id="selectKecamatan" required>
              @foreach($kecamatanList as $kc)
                <option value="{{ $kc }}" {{ old('kecamatan', $location->kecamatan) === $kc ? 'selected' : '' }}>Kec. {{ $kc }}</option>
              @endforeach
            </select>
          @endif
        </div>

        <!-- PILIH LOGO & KATEGORI SECARA VISUAL -->
        <div class="form-group full">
          <label>Pilih Kategori &amp; Logo Pin <span class="req">*</span></label>
          <input type="hidden" name="kategori_id" id="selectedKategoriId" value="{{ old('kategori_id', $location->category_id) }}" required>
          
          @php
            $catIcons = [
              'wisata-alam'       => 'fa-solid fa-mountain-sun',
              'wisata-buatan'     => 'fa-solid fa-landmark-flag',
              'curug'             => 'fa-solid fa-water',
              'sejarah'           => 'fa-solid fa-monument',
              'cafe-kuliner'      => 'fa-solid fa-utensils',
              'minimarket'        => 'fa-solid fa-cart-shopping',
              'spbu'              => 'fa-solid fa-gas-pump',
              'bengkel'           => 'fa-solid fa-wrench',
              'pendidikan'        => 'fa-solid fa-graduation-cap',
              'kesehatan'         => 'fa-solid fa-heart-pulse',
              'umkm-toko'         => 'fa-solid fa-store',
              'pasar'             => 'fa-solid fa-basket-shopping',
              'keamanan'          => 'fa-solid fa-shield-halved',
              'bank-atm'          => 'fa-solid fa-building-columns',
              'transportasi'      => 'fa-solid fa-bus',
              'olahraga-gor'      => 'fa-solid fa-futbol',
              'hotel-penginapan'  => 'fa-solid fa-hotel',
              'ibadah'            => 'fa-solid fa-mosque',
              'pemerintahan'      => 'fa-solid fa-landmark',
            ];
          @endphp

          <div class="category-picker-grid">
            @foreach($categories as $k)
              @php
                $slug = $k->slug;
                $icon = $catIcons[$slug] ?? 'fa-solid fa-map-pin';
                $warna = $k->icon_color ?: '#3B82F6';
                $isActive = (old('kategori_id', $location->category_id) == $k->id);
              @endphp
              <div class="category-card-btn {{ $isActive ? 'active' : '' }}" data-id="{{ $k->id }}" data-slug="{{ $slug }}" data-color="{{ $warna }}" onclick="selectCategory({{ $k->id }}, '{{ $slug }}', '{{ $warna }}')">
                <div class="category-check-badge"><i class="fas fa-check"></i></div>
                <div class="category-card-icon" style="background-color: {{ $warna }};">
                  <i class="{{ $icon }}"></i>
                </div>
                <span class="category-card-label">{{ $k->name }}</span>
              </div>
            @endforeach
          </div>
        </div>

        <div class="form-group">
          <label>Status Penayangan di Peta</label>
          <select name="status">
            <option value="aktif" {{ old('status', $location->is_active ? 'aktif' : 'nonaktif') === 'aktif' ? 'selected' : '' }}>Aktif (Ditampilkan langsung di peta publik)</option>
            <option value="nonaktif" {{ old('status', $location->is_active ? 'aktif' : 'nonaktif') === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Disembunyikan sementara)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Ganti Foto Tempat / Fasilitas</label>
          <input type="file" name="foto_file" accept="image/*">
          @if($location->image)
            <div style="margin-top:6px;display:flex;align-items:center;gap:8px;">
              <img src="{{ $location->image_url }}" alt="Preview" style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
              <small class="form-hint">Foto saat ini aktif.</small>
            </div>
          @endif
        </div>

        <div class="form-group full">
          <label>Alamat Lengkap</label>
          <input type="text" name="alamat" value="{{ old('alamat', $location->address) }}">
        </div>

        <div class="form-group full">
          <label>Deskripsi Ringkas &amp; Fasilitas</label>
          <textarea name="deskripsi" rows="3">{{ old('deskripsi', $location->description) }}</textarea>
        </div>
      </div>
    </div>

    <!-- SECTION 2: MAP & DRAGGABLE PIN REAL-TIME -->
    <div class="form-section">
      <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;">
        <h3 class="form-section-title" style="margin-bottom:0;"><i class="fas fa-location-crosshairs"></i> Posisi Koordinat Peta (Pin Interaktif)</h3>
        <span style="font-size:12px;background:#fdf2fa;color:#9e0b84;padding:3px 10px;border-radius:20px;font-weight:700;">
          <i class="fas fa-hand-pointer"></i> Drag &amp; Drop Pin Didukung
        </span>
      </div>

      <div class="form-grid" style="margin-top:12px;">
        <div class="form-group">
          <label>Latitude <span class="req">*</span></label>
          <input type="text" name="latitude" id="inputLat" value="{{ old('latitude', $location->latitude) }}" required>
        </div>
        <div class="form-group">
          <label>Longitude <span class="req">*</span></label>
          <input type="text" name="longitude" id="inputLng" value="{{ old('longitude', $location->longitude) }}" required>
        </div>
      </div>

      <!-- Shortcuts -->
      <div style="display:flex;align-items:center;gap:6px;margin:12px 0 6px;flex-wrap:wrap;">
        <span style="font-size:11.5px;color:var(--text-muted);font-weight:700;"><i class="fas fa-bolt text-amber-500"></i> Titik Cepat Kec. {{ $targetKec ?? ($userWilayah ?: 'Ciamis') }}:</span>
        @foreach($activeCfg['shortcuts'] as $sc)
          <button type="button" onclick="jumpTo({{ $sc['lat'] }}, {{ $sc['lng'] }})" class="btn-ghost-sm">
            {{ $sc['name'] }}
          </button>
        @endforeach
      </div>

      <!-- Peta Leaflet dengan Floating HUD -->
      <div class="map-hud-container">
        <div class="map-floating-hud">
          <i class="fas fa-arrows-up-down-left-right text-emerald-600"></i>
          <span>Pin Aktif: <strong id="hudCoords" style="font-family:monospace;">{{ $location->latitude }}, {{ $location->longitude }}</strong></span>
        </div>

        <div class="map-boundary-warning" id="boundaryWarning">
          <i class="fas fa-triangle-exclamation"></i>
          <span>Pin berada di luar batas wilayah {{ $userWilayah ?: 'Ciamis' }}! Geser kembali ke dalam area.</span>
        </div>

        <div id="pickMap" style="height:380px;width:100%;"></div>
      </div>
    </div>

    <!-- SECTION 3: TIKET & OPERASIONAL -->
    <div class="form-section">
      <h3 class="form-section-title"><i class="fas fa-clock"></i> Biaya / Harga &amp; Jam Operasional</h3>
      <div class="form-grid">
        <div class="form-group">
          <label>Biaya / Harga Tiket / Tarif</label>
          <input type="text" name="harga" value="{{ old('harga', $location->price_range) }}">
        </div>
        <div class="form-group">
          <label>Nomor Telepon / WhatsApp</label>
          <input type="text" name="no_telepon" value="{{ old('no_telepon', $location->phone) }}">
        </div>
        <div class="form-group">
          <label>Jam Buka</label>
          <input type="time" name="jam_buka" value="{{ old('jam_buka', $location->opening_hours ? substr($location->opening_hours, 0, 5) : '08:00') }}">
        </div>
        <div class="form-group">
          <label>Jam Tutup</label>
          <input type="time" name="jam_tutup" value="{{ old('jam_tutup', $location->closing_hours ? substr($location->closing_hours, 0, 5) : '17:00') }}">
        </div>
        <div class="form-group full">
          <label class="switch-label">
            <div class="switch-wrap">
              <input type="checkbox" name="wifi" id="wifiSwitch" {{ old('wifi', $location->has_wifi) ? 'checked' : '' }}>
              <span class="switch-slider"></span>
            </div>
            <span style="font-size:13px;font-weight:600;color:var(--text-main);">Tersedia Fasilitas Akses Internet / WiFi</span>
          </label>
        </div>
      </div>
    </div>

    <!-- SUBMIT ACTIONS -->
    <div class="form-actions">
      <button type="submit" class="btn-primary btn-lg" id="submitBtn">
        <i class="fas fa-floppy-disk"></i> Simpan Perubahan
      </button>
      <a href="{{ route('admin.locations.index') }}" class="btn-secondary">Batal</a>
    </div>

  </form>
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
const isRegional = @json($isRegional);
const userWilayah = @json($userWilayah);
const wilayahBounds = @json($activeCfg['bounds']);
const defaultLat = {{ (float) ($location->latitude ?: $activeCfg['lat']) }};
const defaultLng = {{ (float) ($location->longitude ?: $activeCfg['lng']) }};
const defaultZoom = {{ $activeCfg['zoom'] }};

const pickMap = L.map('pickMap', {
  center: [defaultLat, defaultLng],
  zoom: defaultZoom
});

L.tileLayer('https://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
  maxZoom: 20,
  subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
  attribution: '&copy; Google Maps'
}).addTo(pickMap);

const inputLat = document.getElementById('inputLat');
const inputLng = document.getElementById('inputLng');
const hudCoords = document.getElementById('hudCoords');
const boundaryWarning = document.getElementById('boundaryWarning');
const submitBtn = document.getElementById('submitBtn');

let pickMarker = null;

function createCustomPin(color = '#9e0b84') {
  return L.divIcon({
    className: 'custom-admin-drag-pin',
    html: `
      <div style="
        width: 38px;
        height: 38px;
        background: ${color};
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.35);
        border: 2.5px solid #ffffff;
        cursor: grab;
      ">
        <i class="fa-solid fa-location-dot" style="transform: rotate(45deg); color: #ffffff; font-size: 17px;"></i>
      </div>
    `,
    iconSize: [38, 38],
    iconAnchor: [19, 38]
  });
}

function checkBoundary(lat, lng) {
  if (!isRegional || !wilayahBounds) return true;
  const isInside = (
    lat >= wilayahBounds.min_lat &&
    lat <= wilayahBounds.max_lat &&
    lng >= wilayahBounds.min_lng &&
    lng <= wilayahBounds.max_lng
  );

  if (!isInside) {
    boundaryWarning.style.display = 'flex';
    submitBtn.style.opacity = '0.5';
    submitBtn.style.pointerEvents = 'none';
    hudCoords.style.color = '#ef4444';
  } else {
    boundaryWarning.style.display = 'none';
    submitBtn.style.opacity = '1';
    submitBtn.style.pointerEvents = 'auto';
    hudCoords.style.color = '#059669';
  }
  return isInside;
}

function updateCoords(lat, lng) {
  inputLat.value = lat.toFixed(7);
  inputLng.value = lng.toFixed(7);
  hudCoords.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
  checkBoundary(lat, lng);
}

const curLat = parseFloat(inputLat.value) || defaultLat;
const curLng = parseFloat(inputLng.value) || defaultLng;

pickMarker = L.marker([curLat, curLng], {
  draggable: true,
  autoPan: true,
  icon: createCustomPin('{{ $location->category->icon_color ?? "#9e0b84" }}')
}).addTo(pickMap);

updateCoords(curLat, curLng);

pickMarker.on('drag', function(e) {
  const pos = e.target.getLatLng();
  updateCoords(pos.lat, pos.lng);
});

pickMarker.on('dragend', function(e) {
  const pos = e.target.getLatLng();
  updateCoords(pos.lat, pos.lng);
});

pickMap.on('click', function(e) {
  pickMarker.setLatLng(e.latlng);
  updateCoords(e.latlng.lat, e.latlng.lng);
});

function jumpTo(lat, lng) {
  pickMap.setView([lat, lng], 16);
  pickMarker.setLatLng([lat, lng]);
  updateCoords(lat, lng);
}

[inputLat, inputLng].forEach(el => {
  el.addEventListener('input', () => {
    const lat = parseFloat(inputLat.value);
    const lng = parseFloat(inputLng.value);
    if (!isNaN(lat) && !isNaN(lng)) {
      pickMarker.setLatLng([lat, lng]);
      pickMap.panTo([lat, lng]);
      updateCoords(lat, lng);
    }
  });
});

function selectCategory(id, slug, color) {
  document.getElementById('selectedKategoriId').value = id;
  document.querySelectorAll('.category-card-btn').forEach(btn => {
    btn.classList.toggle('active', btn.dataset.id == id);
  });
  if (pickMarker) {
    pickMarker.setIcon(createCustomPin(color));
  }
}
</script>
@endpush
