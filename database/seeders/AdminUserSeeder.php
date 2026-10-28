<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Super Administrator',
                'username' => 'superadmin',
                'email' => 'admin@ulinkaciamis.id',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'wilayah' => 'Seluruh Kabupaten Ciamis',
                'is_active' => true,
            ],
            [
                'name' => 'Staff Kec. Panjalu',
                'username' => 'staff_panjalu',
                'email' => 'panjalu@ulinkaciamis.id',
                'password' => Hash::make('password'),
                'role' => 'pengurus_wilayah',
                'wilayah' => 'Panjalu',
                'is_active' => true,
            ],
            [
                'name' => 'Staff Kec. Kawali',
                'username' => 'staff_kawali',
                'email' => 'kawali@ulinkaciamis.id',
                'password' => Hash::make('password'),
                'role' => 'pengurus_wilayah',
                'wilayah' => 'Kawali',
                'is_active' => true,
            ],
            [
                'name' => 'Staff Kec. Sadananya',
                'username' => 'staff_sadananya',
                'email' => 'sadananya@ulinkaciamis.id',
                'password' => Hash::make('password'),
                'role' => 'pengurus_wilayah',
                'wilayah' => 'Sadananya',
                'is_active' => true,
            ],
            [
                'name' => 'Staff Kec. Ciamis Kota',
                'username' => 'staff_ciamis',
                'email' => 'ciamis@ulinkaciamis.id',
                'password' => Hash::make('password'),
                'role' => 'pengurus_wilayah',
                'wilayah' => 'Ciamis',
                'is_active' => true,
            ],
            [
                'name' => 'Perangkat Desa Demo',
                'username' => 'perangkat',
                'email' => 'perangkat@ulinkaciamis.id',
                'password' => Hash::make('password'),
                'role' => 'pengurus_wilayah',
                'wilayah' => 'Ciamis Kota',
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Enrich Location kecamatan from address or name
        $kecamatans = [
            'Panjalu', 'Kawali', 'Sadananya', 'Sindangkasih', 'Banjaranyar', 'Rancah',
            'Cikoneng', 'Panumbangan', 'Cijeungjing', 'Tambaksari', 'Baregbeg', 'Rajadesa',
            'Pamarican', 'Banjarsari', 'Cipaku', 'Lumbung', 'Jatinagara', 'Sukadana',
            'Cisaga', 'Sukamantri', 'Cidolog', 'Cimaragas', 'Lakbok', 'Purwadadi', 'Mangunjaya',
        ];

        foreach (Location::all() as $loc) {
            $found = 'Ciamis';
            foreach ($kecamatans as $kec) {
                if (stripos($loc->address ?? '', $kec) !== false || stripos($loc->name, $kec) !== false) {
                    $found = $kec;
                    break;
                }
            }
            $loc->kecamatan = $found;
            if (empty($loc->features)) {
                $loc->features = $loc->has_wifi ? 'WiFi Kencang, Akses Mudah, Ramah Keluarga' : 'Akses Mudah, Ramah Keluarga, Suasana Asri';
            }
            if (empty($loc->moods)) {
                $loc->moods = 'keluarga, transit, santai';
            }
            if (empty($loc->rating)) {
                $loc->rating = 4.8;
            }
            $loc->save();
        }
    }
}
