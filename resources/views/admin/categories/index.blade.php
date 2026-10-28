@extends('admin.layouts.app')

@section('title', 'Kelola Kategori Wisata')

@section('content')
<div class="page-header">
  <div>
    <h1 class="page-title">Kelola Kategori Direktori</h1>
    <p class="page-sub">Atur kategori tempat, fasilitas umum, warna label, dan urutan tampilan pada peta UlinKa Ciamis.</p>
  </div>
  <button type="button" class="btn-primary" onclick="openAddModal()"><i class="fas fa-plus"></i> Tambah Kategori Baru</button>
</div>

<div class="table-card">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th style="width:40px;">No</th>
          <th>Nama Kategori</th>
          <th>Slug Sistem</th>
          <th>Warna Label</th>
          <th>Urutan</th>
          <th>Total Destinasi</th>
          <th style="text-align:right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
      @foreach ($categories as $index => $cat)
      <tr>
        <td style="color:var(--text-muted);font-weight:600;font-size:12px;">{{ $index + 1 }}</td>
        <td>
          <div style="display:flex;align-items:center;gap:10px;">
            <span style="display:inline-block;width:12px;height:12px;border-radius:50%;background:{{ $cat->icon_color }};box-shadow:0 0 0 2px rgba(0,0,0,0.06);"></span>
            <strong style="color:var(--text-main);font-size:13.5px;">{{ $cat->name }}</strong>
          </div>
        </td>
        <td>
          <code style="background:#f1f5f9;padding:3px 7px;border-radius:4px;font-size:12px;color:var(--text-body);border:1px solid var(--border);">
            {{ $cat->slug }}
          </code>
        </td>
        <td>
          <span class="badge" style="background:{{ $cat->icon_color }}18;color:{{ $cat->icon_color }};border:1px solid {{ $cat->icon_color }}40;">
            {{ $cat->icon_color }}
          </span>
        </td>
        <td><span style="font-weight:600;color:var(--text-body);">{{ $cat->sort_order }}</span></td>
        <td>
          <a href="{{ route('admin.locations.index', ['kat' => $cat->id]) }}" class="badge" style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;text-decoration:none;">
            <i class="fas fa-map-pin" style="font-size:10px;"></i> {{ $cat->locations_count }} Tempat
          </a>
        </td>
        <td style="text-align:right;">
          <div class="tbl-actions" style="justify-content:flex-end;">
            <button type="button" class="tbl-btn edit" title="Edit Kategori" data-id="{{ $cat->id }}" data-nama="{{ $cat->name }}" data-slug="{{ $cat->slug }}" data-warna="{{ $cat->icon_color }}" data-urutan="{{ $cat->sort_order }}" onclick="openEditModal(this.dataset)"><i class="fas fa-pen-to-square"></i></button>
            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori {{ addslashes($cat->name) }}?')">
              @csrf
              @method('DELETE')
              <button type="submit" class="tbl-btn del" title="Hapus Kategori" style="border:none;cursor:pointer;"><i class="fas fa-trash-can"></i></button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL FORM -->
<div class="modal-overlay" id="catModal">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Tambah Kategori</h3>
      <button type="button" class="modal-close" onclick="closeModal()"><i class="fas fa-xmark"></i></button>
    </div>
    <form method="POST" id="catForm" action="{{ route('admin.categories.store') }}">
      @csrf
      <div id="methodContainer"></div>

      <div class="modal-body">
        <div class="form-group" style="margin-bottom:16px;">
          <label>Nama Kategori <span class="req">*</span></label>
          <input type="text" name="nama" id="catNama" placeholder="Contoh: Wisata Religi &amp; Budaya" required oninput="autoSlug(this.value)">
        </div>

        <div class="form-group" style="margin-bottom:16px;">
          <label>Slug URL <span class="req">*</span></label>
          <input type="text" name="slug" id="catSlug" placeholder="cth: wisata-religi-budaya" required>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px;">
          <div class="form-group">
            <label>Warna Aksen</label>
            <div style="display:flex;align-items:center;gap:8px;">
              <input type="color" name="warna" id="catWarna" value="#7a0d66" style="width:42px;height:38px;padding:2px;cursor:pointer;border-radius:6px;border:1px solid var(--border-strong);">
              <input type="text" id="catWarnaText" value="#7a0d66" style="flex:1;" oninput="document.getElementById('catWarna').value=this.value">
            </div>
          </div>
          <div class="form-group">
            <label>Urutan Tampil</label>
            <input type="number" name="urutan" id="catUrutan" value="0" min="0" style="padding:9px 13px;border:1px solid var(--border-strong);border-radius:var(--radius-sm);outline:none;">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary" onclick="closeModal()">Batal</button>
        <button type="submit" class="btn-primary" id="btnSubmit"><i class="fas fa-floppy-disk"></i> Simpan Kategori</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
const modal = document.getElementById('catModal');
const catForm = document.getElementById('catForm');
const methodContainer = document.getElementById('methodContainer');
const catWarna = document.getElementById('catWarna');
const catWarnaText = document.getElementById('catWarnaText');

catWarna.addEventListener('input', e => {
  catWarnaText.value = e.target.value;
});

function openAddModal() {
  document.getElementById('modalTitle').textContent = 'Tambah Kategori Baru';
  catForm.action = "{{ route('admin.categories.store') }}";
  methodContainer.innerHTML = '';
  document.getElementById('catNama').value = '';
  document.getElementById('catSlug').value = '';
  document.getElementById('catWarna').value = '#7a0d66';
  document.getElementById('catWarnaText').value = '#7a0d66';
  document.getElementById('catUrutan').value = '0';
  document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-floppy-disk"></i> Simpan Kategori';
  modal.classList.add('open');
}

function openEditModal(cat) {
  document.getElementById('modalTitle').textContent = 'Edit Kategori: ' + cat.nama;
  catForm.action = "/admin/kategori/" + cat.id;
  methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
  document.getElementById('catNama').value = cat.nama;
  document.getElementById('catSlug').value = cat.slug;
  document.getElementById('catWarna').value = cat.warna || '#7a0d66';
  document.getElementById('catWarnaText').value = cat.warna || '#7a0d66';
  document.getElementById('catUrutan').value = cat.urutan || '0';
  document.getElementById('btnSubmit').innerHTML = '<i class="fas fa-floppy-disk"></i> Simpan Perubahan';
  modal.classList.add('open');
}

function closeModal() {
  modal.classList.remove('open');
}

function autoSlug(text) {
  if (!methodContainer.innerHTML) {
    document.getElementById('catSlug').value = text
      .toLowerCase()
      .trim()
      .replace(/[^\w\s-]/g, '')
      .replace(/[\s_-]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }
}

modal.addEventListener('click', e => {
  if (e.target === modal) closeModal();
});
</script>
@endpush
