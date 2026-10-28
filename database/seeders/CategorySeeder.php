<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = __DIR__.'/categories.json';
        if (! file_exists($jsonPath)) {
            return;
        }

        $categories = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($categories)) {
            return;
        }

        foreach ($categories as $cat) {
            Category::updateOrCreate(
                ['id' => $cat['id']],
                [
                    'name' => $cat['name'],
                    'slug' => $cat['slug'],
                    'icon' => $cat['icon'] ?? '📍',
                    'icon_color' => $cat['icon_color'] ?? $cat['color'] ?? '#7A0D66',
                    'icon_type' => $cat['icon_type'] ?? 'emoji',
                    'sort_order' => $cat['sort_order'] ?? $cat['order'] ?? 0,
                    'is_active' => $cat['is_active'] ?? true,
                ]
            );
        }
    }
}
