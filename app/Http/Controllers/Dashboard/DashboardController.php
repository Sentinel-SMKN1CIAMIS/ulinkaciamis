<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Show the administrator dashboard.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isRegional = $user ? $user->isRegional() : false;
        $userWilayah = $user->wilayah ?? '';

        if ($isRegional) {
            $totalAktif = Location::where('is_active', true)->where('kecamatan', $userWilayah)->count();
            $totalNonaktif = Location::where('is_active', false)->where('kecamatan', $userWilayah)->count();
            $totalAll = Location::where('kecamatan', $userWilayah)->count();
            $totalKategori = Location::where('kecamatan', $userWilayah)->distinct('category_id')->count('category_id');

            $recent = Location::with('category')
                ->where('kecamatan', $userWilayah)
                ->orderByDesc('id')
                ->take(8)
                ->get();

            $byKat = Category::whereHas('locations', function ($q) use ($userWilayah) {
                $q->where('is_active', true)->where('kecamatan', $userWilayah);
            })
                ->withCount(['locations' => function ($q) use ($userWilayah) {
                    $q->where('is_active', true)->where('kecamatan', $userWilayah);
                }])
                ->orderByDesc('locations_count')
                ->take(8)
                ->get();
        } else {
            $totalAktif = Location::where('is_active', true)->count();
            $totalNonaktif = Location::where('is_active', false)->count();
            $totalAll = Location::count();
            $totalKategori = Category::count();

            $recent = Location::with('category')
                ->orderByDesc('id')
                ->take(8)
                ->get();

            $byKat = Category::whereHas('locations', function ($q) {
                $q->where('is_active', true);
            })
                ->withCount(['locations' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->orderByDesc('locations_count')
                ->take(8)
                ->get();
        }

        return view('admin.dashboard', compact(
            'isRegional',
            'userWilayah',
            'totalAktif',
            'totalNonaktif',
            'totalAll',
            'totalKategori',
            'recent',
            'byKat'
        ));
    }
}
