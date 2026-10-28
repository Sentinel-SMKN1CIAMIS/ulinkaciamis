<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    protected array $kecamatanList = [
        'Ciamis', 'Panjalu', 'Kawali', 'Sadananya', 'Cikoneng', 'Sindangkasih', 'Rancah',
        'Cijeungjing', 'Panumbangan', 'Baregbeg', 'Rajadesa', 'Banjaranyar', 'Pamarican',
        'Banjarsari', 'Cipaku', 'Lumbung', 'Jatinagara', 'Sukadana', 'Cisaga', 'Tambaksari',
        'Sukamantri', 'Cidolog', 'Cimaragas', 'Lakbok', 'Purwadadi', 'Mangunjaya',
    ];

    protected array $wilayahConfigs = [
        'Panjalu' => [
            'lat' => -7.135200, 'lng' => 108.281100, 'zoom' => 14,
            'bounds' => ['min_lat' => -7.220, 'max_lat' => -7.050, 'min_lng' => 108.210, 'max_lng' => 108.350],
            'shortcuts' => [
                ['name' => 'Situ Lengkong', 'lat' => -7.135200, 'lng' => 108.281100],
                ['name' => 'Alun-Alun / Pasar Panjalu', 'lat' => -7.138200, 'lng' => 108.281500],
                ['name' => 'Curug 7 Cibolang', 'lat' => -7.120500, 'lng' => 108.267800],
                ['name' => 'Kantor Kec. Panjalu', 'lat' => -7.136800, 'lng' => 108.280500],
                ['name' => 'Puskesmas Panjalu', 'lat' => -7.137800, 'lng' => 108.280100],
                ['name' => 'AHASS Honda Panjalu', 'lat' => -7.139500, 'lng' => 108.282500],
            ],
        ],
        'Kawali' => [
            'lat' => -7.195000, 'lng' => 108.368000, 'zoom' => 14,
            'bounds' => ['min_lat' => -7.260, 'max_lat' => -7.130, 'min_lng' => 108.310, 'max_lng' => 108.430],
            'shortcuts' => [
                ['name' => 'Astana Gede Kawali', 'lat' => -7.195000, 'lng' => 108.368000],
                ['name' => 'Simpang Tiga Kawali', 'lat' => -7.196800, 'lng' => 108.368200],
                ['name' => 'Kantor Kec. Kawali', 'lat' => -7.196500, 'lng' => 108.367500],
                ['name' => 'Puskesmas DTP Kawali', 'lat' => -7.198100, 'lng' => 108.367800],
                ['name' => 'AHASS Kawali', 'lat' => -7.200500, 'lng' => 108.370200],
            ],
        ],
        'Ciamis' => [
            'lat' => -7.326694, 'lng' => 108.353192, 'zoom' => 15,
            'bounds' => ['min_lat' => -7.380, 'max_lat' => -7.260, 'min_lng' => 108.270, 'max_lng' => 108.430],
            'shortcuts' => [
                ['name' => 'Alun-Alun Ciamis', 'lat' => -7.326694, 'lng' => 108.353192],
                ['name' => 'Masjid Agung Ciamis', 'lat' => -7.327150, 'lng' => 108.352480],
                ['name' => 'Stasiun Ciamis', 'lat' => -7.324883, 'lng' => 108.349947],
                ['name' => 'RSUD Ciamis', 'lat' => -7.334750, 'lng' => 108.359400],
                ['name' => 'Stadion Galuh', 'lat' => -7.328900, 'lng' => 108.342100],
            ],
        ],
        'Sadananya' => [
            'lat' => -7.278500, 'lng' => 108.318000, 'zoom' => 14,
            'bounds' => ['min_lat' => -7.320, 'max_lat' => -7.190, 'min_lng' => 108.240, 'max_lng' => 108.360],
            'shortcuts' => [
                ['name' => 'Cadas Ngampar', 'lat' => -7.247997, 'lng' => 108.309501],
                ['name' => 'Curug Panganten', 'lat' => -7.235100, 'lng' => 108.298500],
                ['name' => 'Puskesmas Sadananya', 'lat' => -7.278800, 'lng' => 108.318200],
            ],
        ],
    ];

    /**
     * Display a listing of destinations with filtering and pagination.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isRegional = $user ? $user->isRegional() : false;
        $userWilayah = $user->wilayah ?? '';

        $query = Location::with('category');

        if ($isRegional) {
            $query->where('kecamatan', $userWilayah);
        } elseif ($request->filled('kec') && $request->kec !== 'all') {
            $query->where('kecamatan', $request->kec);
        }

        if ($request->filled('kat') && (int) $request->kat > 0) {
            $query->where('category_id', (int) $request->kat);
        }

        if ($request->filled('q')) {
            $search = '%'.trim($request->q).'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('address', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        $locations = $query->orderByDesc('id')->paginate(15)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();
        $kecamatanList = $this->kecamatanList;

        return view('admin.locations.index', compact(
            'locations',
            'categories',
            'kecamatanList',
            'isRegional',
            'userWilayah'
        ));
    }

    /**
     * Show the form for creating a new destination.
     */
    public function create(): View
    {
        $user = Auth::user();
        $isRegional = $user ? $user->isRegional() : false;
        $userWilayah = $user->wilayah ?? '';

        $categories = Category::orderBy('sort_order')->get();
        $kecamatanList = $this->kecamatanList;

        $activeCfg = $this->wilayahConfigs[$userWilayah] ?? [
            'lat' => -7.326694,
            'lng' => 108.353192,
            'zoom' => 13,
            'bounds' => ['min_lat' => -7.55, 'max_lat' => -7.00, 'min_lng' => 108.15, 'max_lng' => 108.65],
            'shortcuts' => [
                ['name' => 'Alun-Alun Ciamis', 'lat' => -7.326694, 'lng' => 108.353192],
                ['name' => 'Situ Panjalu', 'lat' => -7.135200, 'lng' => 108.281100],
                ['name' => 'Astana Gede Kawali', 'lat' => -7.195000, 'lng' => 108.368000],
                ['name' => 'Cadas Ngampar', 'lat' => -7.247997, 'lng' => 108.309501],
                ['name' => 'Karangkamulyan', 'lat' => -7.351200, 'lng' => 108.489500],
                ['name' => 'Puncak Bangku Rancah', 'lat' => -7.195600, 'lng' => 108.502800],
            ],
        ];

        return view('admin.locations.create', compact(
            'categories',
            'kecamatanList',
            'isRegional',
            'userWilayah',
            'activeCfg'
        ));
    }

    /**
     * Store a newly created destination in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $isRegional = $user ? $user->isRegional() : false;
        $userWilayah = $user->wilayah ?? '';

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:categories,id'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['nullable', 'string', 'max:100'],
            'jam_buka' => ['nullable', 'string', 'max:30'],
            'jam_tutup' => ['nullable', 'string', 'max:30'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'foto_file' => ['nullable', 'image', 'max:5120'],
            'foto' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        $kecamatan = $isRegional ? $userWilayah : ($request->kecamatan ?: 'Ciamis');

        // Handle Image upload or preset
        $imagePath = $request->foto ?: '/ulinkaciamis/assets/images/alun_alun_ciamis.jpg';
        if ($request->hasFile('foto_file')) {
            $file = $request->file('foto_file');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/uploads'), $fileName);
            $imagePath = '/ulinkaciamis/assets/uploads/'.$fileName;
        }

        Location::create([
            'category_id' => $validated['kategori_id'],
            'created_by' => $user->id,
            'name' => $validated['nama'],
            'description' => $validated['deskripsi'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['alamat'] ?? null,
            'kecamatan' => $kecamatan,
            'phone' => $validated['no_telepon'] ?? null,
            'opening_hours' => $validated['jam_buka'] ?? null,
            'closing_hours' => $validated['jam_tutup'] ?? null,
            'has_wifi' => $request->boolean('wifi'),
            'price_range' => $validated['harga'] ?? 'Gratis',
            'image' => $imagePath,
            'is_active' => ($validated['status'] ?? 'aktif') === 'aktif',
            'views' => 0,
            'rating' => 4.8,
            'features' => $request->boolean('wifi') ? 'WiFi Kencang, Akses Mudah, Ramah Keluarga' : 'Akses Mudah, Ramah Keluarga',
            'moods' => 'keluarga, transit',
        ]);

        return redirect()->route('admin.locations.index')
            ->with('success', "Destinasi '{$validated['nama']}' berhasil ditambahkan ke direktori!");
    }

    /**
     * Show the form for editing the specified destination.
     */
    public function edit(Request $request, string|int $id): View|RedirectResponse
    {
        $location = Location::findOrFail($id);
        $user = Auth::user();
        $isRegional = $user ? $user->isRegional() : false;
        $userWilayah = $user->wilayah ?? '';

        if ($isRegional && $location->kecamatan !== $userWilayah) {
            return redirect()->route('admin.locations.index')
                ->with('error', "Akses Ditolak: Anda hanya berwenang mengelola data di Kecamatan {$userWilayah}.");
        }

        $categories = Category::orderBy('sort_order')->get();
        $kecamatanList = $this->kecamatanList;

        $activeCfg = $this->wilayahConfigs[$userWilayah] ?? [
            'lat' => $location->latitude ?: -7.326694,
            'lng' => $location->longitude ?: 108.353192,
            'zoom' => 15,
            'bounds' => ['min_lat' => -7.55, 'max_lat' => -7.00, 'min_lng' => 108.15, 'max_lng' => 108.65],
            'shortcuts' => [
                ['name' => 'Alun-Alun Ciamis', 'lat' => -7.326694, 'lng' => 108.353192],
                ['name' => 'Situ Panjalu', 'lat' => -7.135200, 'lng' => 108.281100],
                ['name' => 'Astana Gede Kawali', 'lat' => -7.195000, 'lng' => 108.368000],
                ['name' => 'Cadas Ngampar', 'lat' => -7.247997, 'lng' => 108.309501],
            ],
        ];

        return view('admin.locations.edit', compact(
            'location',
            'categories',
            'kecamatanList',
            'isRegional',
            'userWilayah',
            'activeCfg'
        ));
    }

    /**
     * Update the specified destination in storage.
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        $location = Location::findOrFail($id);
        $user = Auth::user();
        $isRegional = $user ? $user->isRegional() : false;
        $userWilayah = $user->wilayah ?? '';

        if ($isRegional && $location->kecamatan !== $userWilayah) {
            return redirect()->route('admin.locations.index')
                ->with('error', "Akses Ditolak: Anda hanya berwenang mengelola data di Kecamatan {$userWilayah}.");
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:categories,id'],
            'latitude' => ['required', 'numeric'],
            'longitude' => ['required', 'numeric'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'kecamatan' => ['nullable', 'string', 'max:100'],
            'deskripsi' => ['nullable', 'string'],
            'harga' => ['nullable', 'string', 'max:100'],
            'jam_buka' => ['nullable', 'string', 'max:30'],
            'jam_tutup' => ['nullable', 'string', 'max:30'],
            'no_telepon' => ['nullable', 'string', 'max:30'],
            'foto_file' => ['nullable', 'image', 'max:5120'],
            'foto' => ['nullable', 'string', 'max:500'],
            'status' => ['nullable', 'in:aktif,nonaktif'],
        ]);

        $kecamatan = $isRegional ? $userWilayah : ($request->kecamatan ?: $location->kecamatan);

        $imagePath = $location->image;
        if ($request->hasFile('foto_file')) {
            $file = $request->file('foto_file');
            $fileName = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('assets/uploads'), $fileName);
            $imagePath = '/ulinkaciamis/assets/uploads/'.$fileName;
        } elseif (! empty($request->foto)) {
            $imagePath = $request->foto;
        }

        $location->update([
            'category_id' => $validated['kategori_id'],
            'name' => $validated['nama'],
            'description' => $validated['deskripsi'] ?? null,
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'address' => $validated['alamat'] ?? null,
            'kecamatan' => $kecamatan,
            'phone' => $validated['no_telepon'] ?? null,
            'opening_hours' => $validated['jam_buka'] ?? null,
            'closing_hours' => $validated['jam_tutup'] ?? null,
            'has_wifi' => $request->boolean('wifi'),
            'price_range' => $validated['harga'] ?? 'Gratis',
            'image' => $imagePath,
            'is_active' => ($validated['status'] ?? 'aktif') === 'aktif',
        ]);

        return redirect()->route('admin.locations.index')
            ->with('success', "Destinasi '{$validated['nama']}' berhasil diperbarui!");
    }

    /**
     * Remove the specified destination from storage.
     */
    public function destroy(Request $request, string|int $id): RedirectResponse
    {
        $location = Location::findOrFail($id);
        $user = Auth::user();
        $isRegional = $user ? $user->isRegional() : false;
        $userWilayah = $user->wilayah ?? '';

        if ($isRegional && $location->kecamatan !== $userWilayah) {
            return redirect()->route('admin.locations.index')
                ->with('error', "Akses Ditolak: Anda tidak memiliki hak menghapus data di luar wilayah {$userWilayah}.");
        }

        $nama = $location->name;
        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', "Destinasi '{$nama}' berhasil dihapus.");
    }
}
