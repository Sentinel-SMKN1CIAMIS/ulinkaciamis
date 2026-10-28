<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DestinationController extends Controller
{
    /**
     * Get destinations list with optional filtering.
     */
    public function index(Request $request): JsonResponse
    {
        $kategoriId = (int) $request->query('kategori', 0);
        $kategoriSlug = trim((string) $request->query('kategori_slug', ''));
        $kecamatan = trim((string) $request->query('kecamatan', ''));
        $search = trim((string) $request->query('q', ''));

        $query = Location::with('category')->where('is_active', true);

        if ($kategoriId > 0) {
            $query->where('category_id', $kategoriId);
        } elseif (! empty($kategoriSlug) && $kategoriSlug !== 'all') {
            $query->whereHas('category', function ($q) use ($kategoriSlug) {
                $q->where('slug', $kategoriSlug);
            });
        }

        if (! empty($kecamatan) && $kecamatan !== 'all') {
            $query->where('kecamatan', $kecamatan);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $like = "%{$search}%";
                $q->where('name', 'like', $like)
                    ->orWhere('address', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('kecamatan', 'like', $like);
            });
        }

        $locations = $query->orderByDesc('id')->get();
        $fallbackImage = 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?w=700&auto=format&fit=crop';

        $data = $locations->map(function (Location $loc) use ($fallbackImage) {
            $hours = $loc->opening_hours ?: 'Setiap Hari';
            if ($loc->closing_hours && ! str_contains($hours, '-')) {
                $hours = $loc->opening_hours.' - '.$loc->closing_hours.' WIB';
            }

            $features = [];
            if (! empty($loc->features)) {
                $features = array_values(array_filter(array_map('trim', explode(',', $loc->features))));
            }
            if (empty($features)) {
                if ($loc->has_wifi) {
                    $features[] = 'WiFi Kencang';
                }
                $features[] = 'Akses Mudah';
                $features[] = 'Ramah Keluarga';
            }

            $moods = [];
            if (! empty($loc->moods)) {
                $moods = array_values(array_filter(array_map('trim', explode(',', $loc->moods))));
            }
            if (empty($moods)) {
                $moods = ['keluarga', 'transit'];
            }

            $img = $loc->image_url ?: $fallbackImage;
            $cat = $loc->category;

            return [
                'id' => (int) $loc->id,
                'nama' => $loc->name,
                'name' => $loc->name,
                'category' => $cat->slug ?? 'wisata-alam',
                'categoryName' => $cat->name ?? 'Wisata Alam',
                'kecamatan' => $loc->kecamatan ?: 'Ciamis',
                'location' => $loc->address ?: 'Kabupaten Ciamis',
                'deskripsi' => $loc->description ?? '',
                'desc' => $loc->description ?? '',
                'alamat' => $loc->address ?? '',
                'latitude' => (float) $loc->latitude,
                'longitude' => (float) $loc->longitude,
                'lat' => (float) $loc->latitude,
                'lng' => (float) $loc->longitude,
                'harga' => $loc->price_range ?: 'Gratis',
                'price' => $loc->price_range ?: 'Gratis',
                'wifi' => (bool) $loc->has_wifi,
                'jam_buka' => $loc->opening_hours ? substr($loc->opening_hours, 0, 5) : null,
                'jam_tutup' => $loc->closing_hours ? substr($loc->closing_hours, 0, 5) : null,
                'hours' => $hours,
                'rating' => (float) ($loc->rating ?: 4.8),
                'features' => $features,
                'moods' => $moods,
                'image' => $img,
                'foto' => $img,
                'no_telepon' => $loc->phone,
                'kategori_id' => (int) $loc->category_id,
                'kategori_nama' => $cat->name ?? 'Wisata Alam',
                'kategori_slug' => $cat->slug ?? 'wisata-alam',
                'kategori_warna' => $cat->icon_color ?? '#3B82F6',
                'ditambahkan_oleh' => $loc->creator->name ?? 'Admin',
            ];
        });

        return response()->json([
            'status' => 'ok',
            'data' => $data,
            'total' => $data->count(),
        ]);
    }

    /**
     * Get categories list for API clients.
     */
    public function categories(): JsonResponse
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Category $c) => [
                'id' => (int) $c->id,
                'nama' => $c->name,
                'name' => $c->name,
                'slug' => $c->slug,
                'warna' => $c->icon_color,
                'color' => $c->icon_color,
                'urutan' => (int) $c->sort_order,
            ]);

        return response()->json([
            'status' => 'ok',
            'data' => $categories,
            'total' => $categories->count(),
        ]);
    }
}
