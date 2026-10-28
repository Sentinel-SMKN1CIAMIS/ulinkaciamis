<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonPath = __DIR__.'/locations.json';
        if (! file_exists($jsonPath)) {
            return;
        }

        $locations = json_decode(file_get_contents($jsonPath), true);
        if (! is_array($locations)) {
            return;
        }

        foreach ($locations as $loc) {
            Location::updateOrCreate(
                ['id' => $loc['id']],
                [
                    'category_id' => $loc['category_id'],
                    'created_by' => $loc['created_by'] ?? 1,
                    'name' => $loc['name'],
                    'kecamatan' => $loc['kecamatan'] ?? 'Ciamis',
                    'description' => $loc['description'] ?? null,
                    'address' => $loc['address'] ?? null,
                    'latitude' => $loc['latitude'],
                    'longitude' => $loc['longitude'],
                    'image' => $loc['image'] ?? null,
                    'opening_hours' => $loc['opening_hours'] ?? null,
                    'price_range' => $loc['price_range'] ?? $loc['price'] ?? null,
                    'rating' => $loc['rating'] ?? 4.8,
                    'features' => $loc['features'] ?? null,
                    'moods' => $loc['moods'] ?? null,
                    'phone' => $loc['phone'] ?? null,
                    'website' => $loc['website'] ?? null,
                    'has_wifi' => (bool) ($loc['has_wifi'] ?? false),
                    'is_active' => (bool) ($loc['is_active'] ?? true),
                ]
            );
        }
    }
}
