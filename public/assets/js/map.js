/* ============================================================
   UlinKa Ciamis - map.js
   Leaflet interactive map with categories & detail panel
   ============================================================ */

const MAP_CENTER = [-7.3276, 108.3625];
const MAP_ZOOM   = 12;

// ── CATEGORY ICONS & EMOJIS ─────────────────────────────────
const CATEGORY_ICONS = {
  'wisata-alam':      { emoji: '🏞️', color: '#10B981' },
  'wisata-buatan':    { emoji: '🎡', color: '#6366F1' },
  'cafe-kuliner':     { emoji: '☕', color: '#F59E0B' },
  'minimarket':       { emoji: '🏪', color: '#EF4444' },
  'spbu':             { emoji: '⛽', color: '#F97316' },
  'umkm-toko':        { emoji: '🛒', color: '#8B5CF6' },
  'pasar':            { emoji: '🏬', color: '#EC4899' },
  'kesehatan':        { emoji: '🏥', color: '#06B6D4' },
  'pendidikan':       { emoji: '🏫', color: '#3B82F6' },
  'keamanan':         { emoji: '🚔', color: '#64748B' },
  'bank-atm':         { emoji: '🏦', color: '#0EA5E9' },
  'transportasi':     { emoji: '🚌', color: '#84CC16' },
  'olahraga-gor':     { emoji: '🏟️', color: '#F43F5E' },
  'hotel-penginapan': { emoji: '🏨', color: '#A855F7' },
  'ibadah':           { emoji: '🕌', color: '#059669' },
  'pemerintahan':     { emoji: '🏛️', color: '#475569' },
  'default':          { emoji: '📍', color: '#94A3B8' },
};

// ── CIAMIS MAP BOUNDARIES ──────────────────────────────────
// Batas koordinat Kabupaten Ciamis untuk membatasi render peta
const CIAMIS_BOUNDS = L.latLngBounds(
  [-7.75, 108.10], // South-West (Pangandaran boundary)
  [-7.00, 108.65]  // North-East (Cirebon / Kuningan boundary)
);

function getCatInfo(slug) {
  return CATEGORY_ICONS[slug] || CATEGORY_ICONS['default'];
}

// Brand-aware icon customization (Alfamart, Indomaret, SPBU, Mixue, Momoyo, dll.)
function getPlaceIconInfo(place) {
  const name = (place.nama || '').toLowerCase();
  
  if (name.includes('alfamart')) {
    return { emoji: '🏪', color: '#D90429', brand: 'Alfamart' };
  }
  if (name.includes('indomaret')) {
    return { emoji: '🏪', color: '#005F73', brand: 'Indomaret' };
  }
  if (name.includes('spbu') || name.includes('pertamina')) {
    return { emoji: '⛽', color: '#E85D04', brand: 'SPBU' };
  }
  if (name.includes('mixue') || name.includes('momoyo')) {
    return { emoji: '🍦', color: '#E63946', brand: 'Ice Cream & Tea' };
  }
  if (name.includes('kolam renang') || name.includes('waterboom') || name.includes('tirta')) {
    return { emoji: '🏊', color: '#00B4D8', brand: 'Kolam Renang' };
  }
  if (name.includes('curug') || name.includes('sungai') || name.includes('situ')) {
    return { emoji: '🏞️', color: '#10B981', brand: 'Wisata Air' };
  }
  if (name.includes('perpustakaan') || name.includes('perpus')) {
    return { emoji: '📚', color: '#F77F00', brand: 'Perpustakaan' };
  }
  if (name.includes('polsek') || name.includes('polres')) {
    return { emoji: '🚔', color: '#1D3557', brand: 'Kepolisian' };
  }
  if (name.includes('stadion') || name.includes('gor')) {
    return { emoji: '🏟️', color: '#D62828', brand: 'Olahraga' };
  }
  if (name.includes('terminal') || name.includes('halte')) {
    return { emoji: '🚌', color: '#588157', brand: 'Transportasi' };
  }
  
  return getCatInfo(place.kategori_slug);
}

// ── INIT MAP ─────────────────────────────────────────────────
const map = L.map('map', {
  center: MAP_CENTER,
  zoom: MAP_ZOOM,
  minZoom: 10,
  maxZoom: 18,
  maxBounds: CIAMIS_BOUNDS,
  maxBoundsViscosity: 0.85,
  zoomControl: true,
  attributionControl: false,
});

L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
  maxZoom: 19,
}).addTo(map);

// Visual Ciamis boundary circle
L.circle(MAP_CENTER, {
  color: '#3B82F6',
  fillColor: '#3B82F6',
  fillOpacity: 0.02,
  weight: 2,
  dashArray: '6, 8',
  radius: 22000
}).addTo(map);

// Ciamis boundary label
L.marker([-7.3276, 108.3625], {
  icon: L.divIcon({
    html: '<div class="city-label">📍 Wilayah Ciamis</div>',
    className: '',
    iconSize: [160, 30],
    iconAnchor: [80, 15],
  })
}).addTo(map);

// ── STATE ────────────────────────────────────────────────────
let allData     = [];
let allMarkers  = [];
let markerLayer = L.layerGroup().addTo(map);
let activeKat   = 0;
let searchQ     = '';

// ── CREATE MARKER ICON ────────────────────────────────────────
function createIcon(place) {
  const info = getPlaceIconInfo(place);
  return L.divIcon({
    html: `<div class="custom-marker" style="background:${info.color}" title="${escHtml(place.nama)}">
             <div class="marker-inner">${info.emoji}</div>
           </div>`,
    className: '',
    iconSize: [36, 36],
    iconAnchor: [18, 36],
    popupAnchor: [0, -38],
  });
}

// ── RENDER MARKERS ────────────────────────────────────────────
function renderMarkers(data) {
  markerLayer.clearLayers();
  allMarkers = [];

  const noResult = document.getElementById('noResult');
  if (data.length === 0) {
    noResult.style.display = 'flex';
    document.getElementById('totalCount').textContent = 0;
    return;
  }
  noResult.style.display = 'none';
  document.getElementById('totalCount').textContent = data.length;

  data.forEach(place => {
    const icon   = createIcon(place);
    const info   = getPlaceIconInfo(place);
    const marker = L.marker([place.latitude, place.longitude], { icon })
      .addTo(markerLayer);

    const jam = place.jam_buka && place.jam_tutup
      ? `${place.jam_buka} - ${place.jam_tutup}`
      : 'Tidak tersedia';

    marker.bindPopup(`
      ${place.foto ? `<img src="${escHtml(place.foto)}" alt="${escHtml(place.nama)}" style="width:100%;height:100px;object-fit:cover;border-radius:8px;margin-bottom:8px">` : ''}
      <div class="popup-name">${escHtml(place.nama)}</div>
      <div class="popup-cat">${info.emoji} ${escHtml(place.kategori_nama)}</div>
      <div style="font-size:11px;color:#94a3b8;margin-bottom:4px"><i class="fas fa-clock"></i> ${escHtml(jam)}</div>
      ${place.wifi ? '<div style="font-size:11px;color:#38BDF8;margin-bottom:4px"><i class="fas fa-wifi"></i> WiFi Tersedia</div>' : ''}
      ${place.harga ? `<div style="font-size:12px;color:#10B981;font-weight:600;margin-bottom:6px">🎟️ ${escHtml(place.harga)}</div>` : ''}
      <div style="font-size:10px;color:#64748b;margin-bottom:8px">📍 ${Number(place.latitude).toFixed(4)}, ${Number(place.longitude).toFixed(4)}</div>
      <button class="popup-btn" onclick="showDetail(${place.id})">
        <i class="fas fa-info-circle"></i> Lihat Detail
      </button>
    `, { maxWidth: 230 });

    marker._placeData = place;
    allMarkers.push(marker);
  });
}

// ── FILTER ───────────────────────────────────────────────────
function applyFilter() {
  let filtered = allData;
  if (activeKat > 0) {
    filtered = filtered.filter(d => d.kategori_id === activeKat);
  }
  if (searchQ.length > 1) {
    const q = searchQ.toLowerCase();
    filtered = filtered.filter(d =>
      d.nama.toLowerCase().includes(q) ||
      (d.alamat && d.alamat.toLowerCase().includes(q)) ||
      (d.deskripsi && d.deskripsi.toLowerCase().includes(q))
    );
  }
  renderMarkers(filtered);
}

// ── LOAD DATA ─────────────────────────────────────────────────
async function loadData() {
  try {
    const r = await fetch('/ulinkaciamis/api/destinasi.php');
    const j = await r.json();
    allData = j.data || [];
    document.getElementById('totalCount').textContent = allData.length;
    renderMarkers(allData);
  } catch(e) {
    console.error('Load data error:', e);
  } finally {
    document.getElementById('mapLoading').style.display = 'none';
  }
}

async function loadKategori() {
  try {
    const r = await fetch('/ulinkaciamis/api/kategori.php');
    const j = await r.json();
    const list = document.getElementById('categoryList');
    const counts = {};
    allData.forEach(d => { counts[d.kategori_id] = (counts[d.kategori_id]||0)+1; });

    j.data.forEach(kat => {
      const info = getCatInfo(kat.slug);
      const btn  = document.createElement('button');
      btn.className  = 'cat-btn';
      btn.dataset.id = kat.id;
      btn.innerHTML  = `
        <span class="cat-icon">${info.emoji}</span>
        <span class="cat-label">${escHtml(kat.nama)}</span>
        <span class="cat-count" id="count-${kat.id}">-</span>
      `;
      btn.addEventListener('click', () => selectKategori(kat.id, btn));
      list.appendChild(btn);
    });

    // update counts after data loaded
    setTimeout(updateCounts, 800);
  } catch(e) {
    console.error('Load kategori error:', e);
  }
}

function updateCounts() {
  const counts = {};
  allData.forEach(d => { counts[d.kategori_id] = (counts[d.kategori_id]||0)+1; });
  const allEl = document.getElementById('count-0');
  if (allEl) allEl.textContent = allData.length;
  Object.entries(counts).forEach(([id, cnt]) => {
    const el = document.getElementById('count-' + id);
    if (el) el.textContent = cnt;
  });
}

// ── KATEGORI SELECT ───────────────────────────────────────────
function selectKategori(id, btn) {
  activeKat = id;
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  applyFilter();
}

// ── DETAIL PANEL ──────────────────────────────────────────────
function showDetail(id) {
  const place = allData.find(d => d.id === id);
  if (!place) return;

  const info = getPlaceIconInfo(place);
  const jam = (place.jam_buka && place.jam_tutup)
    ? `${place.jam_buka} – ${place.jam_tutup}`
    : 'Tidak tersedia';
  const mapsUrl = `https://www.google.com/maps?q=${place.latitude},${place.longitude}`;

  document.getElementById('detailBody').innerHTML = `
    ${place.foto ? `<img src="${escHtml(place.foto)}" alt="${escHtml(place.nama)}" style="width:100%;height:180px;object-fit:cover;border-radius:12px;margin-bottom:14px;border:1px solid rgba(255,255,255,.1)">` : ''}
    <div class="detail-cat-badge" style="background:${info.color}22;color:${info.color};border:1px solid ${info.color}44">
      ${info.emoji} ${escHtml(place.kategori_nama)} ${info.brand ? `• ${info.brand}` : ''}
    </div>
    <div class="detail-name">${escHtml(place.nama)}</div>
    <div class="detail-address">
      <i class="fas fa-location-dot"></i>
      <span>${escHtml(place.alamat || 'Alamat tidak tersedia')}</span>
    </div>

    <div class="detail-grid">
      <div class="detail-info-card">
        <div class="info-label">⏰ Jam Buka</div>
        <div class="info-value">${escHtml(jam)}</div>
      </div>
      <div class="detail-info-card">
        <div class="info-label">🎟️ Harga</div>
        <div class="info-value">${place.harga ? escHtml(place.harga) : 'Tidak ada'}</div>
      </div>
    </div>

    <div class="detail-info-full">
      <div class="info-label">📶 WiFi</div>
      <div class="info-value">
        ${place.wifi
          ? '<span class="badge-wifi-yes"><i class="fas fa-wifi"></i> Tersedia WiFi</span>'
          : '<span class="badge-wifi-no"><i class="fas fa-wifi-slash"></i> Tidak ada WiFi</span>'}
      </div>
    </div>

    ${place.no_telepon ? `
    <div class="detail-info-full">
      <div class="info-label">📞 Telepon</div>
      <div class="info-value">${escHtml(place.no_telepon)}</div>
    </div>` : ''}

    ${place.deskripsi ? `
    <div class="detail-info-full">
      <div class="info-label">ℹ️ Deskripsi</div>
      <div class="info-value">${escHtml(place.deskripsi)}</div>
    </div>` : ''}

    <div class="detail-coords">
      <i class="fas fa-crosshairs"></i>
      ${place.latitude.toFixed(6)}, ${place.longitude.toFixed(6)}
    </div>

    <div class="detail-actions">
      <a href="${mapsUrl}" target="_blank" class="btn-maps">
        <i class="fas fa-map-location-dot"></i> Buka di Google Maps
      </a>
      <button class="btn-share" onclick="sharePlace('${escHtml(place.nama)}','${mapsUrl}')" title="Bagikan">
        <i class="fas fa-share-nodes"></i>
      </button>
    </div>
  `;

  document.getElementById('detailPanel').classList.add('active');
  document.getElementById('detailOverlay').classList.add('active');
  map.setView([place.latitude, place.longitude], 16, { animate: true });
}

function closeDetail() {
  document.getElementById('detailPanel').classList.remove('active');
  document.getElementById('detailOverlay').classList.remove('active');
}

async function sharePlace(name, url) {
  if (navigator.share) {
    await navigator.share({ title: `${name} - UlinKa Ciamis`, url });
  } else {
    navigator.clipboard.writeText(url).then(() => alert('Link disalin!'));
  }
}

// ── SEARCH ────────────────────────────────────────────────────
const searchInput = document.getElementById('searchInput');
const searchClear = document.getElementById('searchClear');
let searchTimer;

searchInput.addEventListener('input', e => {
  searchQ = e.target.value.trim();
  searchClear.style.display = searchQ ? 'flex' : 'none';
  clearTimeout(searchTimer);
  searchTimer = setTimeout(applyFilter, 350);
});
searchClear.addEventListener('click', () => {
  searchInput.value = '';
  searchQ = '';
  searchClear.style.display = 'none';
  applyFilter();
  searchInput.focus();
});

// ── CONTROLS ──────────────────────────────────────────────────
document.getElementById('btnResetView').addEventListener('click', () => {
  map.setView(MAP_CENTER, MAP_ZOOM, { animate: true });
});
document.getElementById('btnMyLocation').addEventListener('click', () => {
  map.locate({ setView: true, maxZoom: 16 });
});
map.on('locationfound', e => {
  L.marker(e.latlng, {
    icon: L.divIcon({
      html: '<div style="width:16px;height:16px;background:#3B82F6;border:3px solid #fff;border-radius:50%;box-shadow:0 0 12px #3B82F6"></div>',
      className: '',
      iconSize: [16, 16],
      iconAnchor: [8, 8],
    })
  }).addTo(map).bindPopup('📍 Lokasi Anda').openPopup();
});

document.getElementById('btnMyLocation').addEventListener('click', () => {
  map.locate({ setView: true, maxZoom: 16 });
});

// Sidebar mobile toggle
document.getElementById('btnSidebar').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
});
document.getElementById('sidebarClose').addEventListener('click', () => {
  document.getElementById('sidebar').classList.remove('open');
});

// All kategori button
document.querySelector('.cat-btn[data-id="0"]').addEventListener('click', function() {
  activeKat = 0;
  document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
  this.classList.add('active');
  applyFilter();
});

// Detail panel close
document.getElementById('detailClose').addEventListener('click', closeDetail);
document.getElementById('detailOverlay').addEventListener('click', closeDetail);

// ── HELPERS ───────────────────────────────────────────────────
function escHtml(str) {
  if (!str) return '';
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/"/g,'&quot;');
}

// ── BOOT ──────────────────────────────────────────────────────
(async () => {
  await loadKategori();
  await loadData();
  updateCounts();
})();

// Expose globally for popup buttons
window.showDetail = showDetail;
