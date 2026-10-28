<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): View
    {
        $categories = Category::withCount('locations')
            ->orderBy('sort_order')
            ->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:categories,slug'],
            'warna' => ['nullable', 'string', 'max:10'],
            'urutan' => ['nullable', 'integer'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['nama']);

        Category::create([
            'name' => $validated['nama'],
            'slug' => $slug,
            'icon_color' => $validated['warna'] ?: '#3B82F6',
            'sort_order' => (int) ($validated['urutan'] ?? 0),
            'is_active' => true,
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$validated['nama']}' berhasil ditambahkan!");
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:100', 'unique:categories,slug,'.$category->id],
            'warna' => ['nullable', 'string', 'max:10'],
            'urutan' => ['nullable', 'integer'],
        ]);

        $slug = ! empty($validated['slug'])
            ? Str::slug($validated['slug'])
            : Str::slug($validated['nama']);

        $category->update([
            'name' => $validated['nama'],
            'slug' => $slug,
            'icon_color' => $validated['warna'] ?: $category->icon_color,
            'sort_order' => (int) ($validated['urutan'] ?? $category->sort_order),
        ]);

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$validated['nama']}' berhasil diperbarui!");
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, string|int $id): RedirectResponse
    {
        $category = Category::withCount('locations')->findOrFail($id);

        if ($category->locations_count > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$category->locations_count} destinasi.");
        }

        $name = $category->name;
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Kategori '{$name}' berhasil dihapus.");
    }
}
