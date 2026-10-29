<?php

use App\Http\Controllers\Api\DestinationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\LocationController;
use App\Http\Controllers\MapController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Halaman Peta & Eksplorasi Publik
Route::get('/', [MapController::class, 'index'])->name('home');
Route::get('/ulinkaciamis', [MapController::class, 'index']);
Route::get('/ulinkaciamis/', [MapController::class, 'index']);

// API Destinasi & Kategori
Route::get('/api/destinasi', [DestinationController::class, 'index'])->name('api.destinasi');
Route::any('/api/destinasi.php', [DestinationController::class, 'index']);
Route::any('/ulinkaciamis/api/destinasi.php', [DestinationController::class, 'index']);
Route::any('/ulinkaciamis/api/destinasi', [DestinationController::class, 'index']);

Route::get('/api/kategori', [DestinationController::class, 'categories'])->name('api.kategori');
Route::any('/api/kategori.php', [DestinationController::class, 'categories']);
Route::any('/ulinkaciamis/api/kategori.php', [DestinationController::class, 'categories']);
Route::any('/ulinkaciamis/api/kategori', [DestinationController::class, 'categories']);

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/admin/logout', [LoginController::class, 'logout']);
Route::get('/admin/logout.php', [LoginController::class, 'logout']);

// Aliases for legacy subfolder logins
Route::get('/ulinkaciamis/login', [LoginController::class, 'showLoginForm']);
Route::post('/ulinkaciamis/login', [LoginController::class, 'login']);
Route::get('/ulinkaciamis/admin/login.php', [LoginController::class, 'showLoginForm']);
Route::post('/ulinkaciamis/admin/login.php', [LoginController::class, 'login']);
Route::get('/admin/login.php', [LoginController::class, 'showLoginForm']);
Route::post('/admin/login.php', [LoginController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Administrator Dashboard & Management (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/admin', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'index']);
    Route::get('/admin/dashboard.php', [DashboardController::class, 'index']);
    Route::get('/ulinkaciamis/admin', [DashboardController::class, 'index']);
    Route::get('/ulinkaciamis/admin/dashboard.php', [DashboardController::class, 'index']);

    // Locations Management
    Route::get('/admin/kelola', [LocationController::class, 'index'])->name('admin.locations.index');
    Route::get('/admin/kelola.php', function (Request $request, LocationController $controller) {
        if ($request->has('hapus')) {
            return $controller->destroy($request, $request->query('hapus'));
        }

        return $controller->index($request);
    });
    Route::get('/ulinkaciamis/admin/kelola.php', function (Request $request, LocationController $controller) {
        if ($request->has('hapus')) {
            return $controller->destroy($request, $request->query('hapus'));
        }

        return $controller->index($request);
    });

    Route::get('/admin/tambah', [LocationController::class, 'create'])->name('admin.locations.create');
    Route::get('/admin/tambah.php', [LocationController::class, 'create']);
    Route::get('/ulinkaciamis/admin/tambah.php', [LocationController::class, 'create']);
    Route::post('/admin/tambah', [LocationController::class, 'store'])->name('admin.locations.store');
    Route::post('/admin/tambah.php', [LocationController::class, 'store']);
    Route::post('/ulinkaciamis/admin/tambah.php', [LocationController::class, 'store']);

    Route::get('/admin/edit/{id}', [LocationController::class, 'edit'])->name('admin.locations.edit');
    Route::get('/admin/edit.php', function (Request $request, LocationController $controller) {
        return $controller->edit($request, $request->query('id', 0));
    });
    Route::get('/ulinkaciamis/admin/edit.php', function (Request $request, LocationController $controller) {
        return $controller->edit($request, $request->query('id', 0));
    });
    Route::match(['put', 'post'], '/admin/locations/{id}', [LocationController::class, 'update'])->name('admin.locations.update');
    Route::match(['put', 'post'], '/ulinkaciamis/admin/locations/{id}', [LocationController::class, 'update']);
    Route::match(['put', 'post'], '/admin/edit.php', function (Request $request, LocationController $controller) {
        return $controller->update($request, $request->query('id', $request->input('id', 0)));
    });
    Route::match(['put', 'post'], '/ulinkaciamis/admin/edit.php', function (Request $request, LocationController $controller) {
        return $controller->update($request, $request->query('id', $request->input('id', 0)));
    });
    Route::delete('/admin/locations/{id}', [LocationController::class, 'destroy'])->name('admin.locations.destroy');
    Route::match(['delete', 'post'], '/ulinkaciamis/admin/locations/{id}', [LocationController::class, 'destroy']);

    // Category Management
    Route::get('/admin/kategori', [CategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/kategori.php', function (Request $request, CategoryController $controller) {
        if ($request->has('hapus')) {
            return $controller->destroy($request, $request->query('hapus'));
        }

        return $controller->index();
    });
    Route::get('/ulinkaciamis/admin/kategori.php', function (Request $request, CategoryController $controller) {
        if ($request->has('hapus')) {
            return $controller->destroy($request, $request->query('hapus'));
        }

        return $controller->index();
    });
    Route::post('/admin/kategori', [CategoryController::class, 'store'])->name('admin.categories.store');
    Route::post('/admin/kategori.php', [CategoryController::class, 'store']);
    Route::put('/admin/kategori/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/kategori/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
});

/*
|--------------------------------------------------------------------------
| Static Assets Handler (Support both standard assets and /ulinkaciamis/ prefix)
|--------------------------------------------------------------------------
*/

if (! function_exists('serveLaravelAsset')) {
    function serveLaravelAsset(string $relativePath)
    {
        $filePath = public_path('assets/'.$relativePath);
        if (! file_exists($filePath) || is_dir($filePath)) {
            // Fallback to base assets if not yet copied
            $fallback = base_path('assets/'.$relativePath);
            if (file_exists($fallback) && ! is_dir($fallback)) {
                $filePath = $fallback;
            } else {
                abort(404);
            }
        }

        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $mimes = [
            'webp' => 'image/webp',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
        ];

        $mime = $mimes[$ext] ?? mime_content_type($filePath);

        return response()->file($filePath, ['Content-Type' => $mime]);
    }
}

Route::get('/ulinkaciamis/assets/{path}', function (string $path) {
    return serveLaravelAsset($path);
})->where('path', '.*');

Route::get('/assets/{path}', function (string $path) {
    return serveLaravelAsset($path);
})->where('path', '.*');

Route::get('/ulinkaciamis/public/images/{path}', function (string $path) {
    $filePath = public_path('images/'.$path);
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*');

Route::get('/images/{path}', function (string $path) {
    $filePath = public_path('images/'.$path);
    if (! file_exists($filePath)) {
        abort(404);
    }

    return response()->file($filePath);
})->where('path', '.*');
