<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UlinKaCiamisTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('UlinKa Ciamis');
    }

    public function test_ulinkaciamis_alias_loads_successfully(): void
    {
        $response = $this->get('/ulinkaciamis');
        $response->assertStatus(200);
        $response->assertSee('UlinKa Ciamis');
    }

    public function test_api_destinations_returns_valid_json(): void
    {
        $response = $this->get('/api/destinasi');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'category',
                    'categoryName',
                    'kecamatan',
                    'location',
                    'latitude',
                    'longitude',
                    'hours',
                    'rating',
                    'image',
                ],
            ],
            'total',
        ]);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_legacy_api_destinasi_php_endpoint_works(): void
    {
        $response = $this->get('/api/destinasi.php');
        $response->assertStatus(200);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_api_categories_returns_valid_json(): void
    {
        $response = $this->get('/api/kategori');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'data' => [
                '*' => [
                    'id',
                    'nama',
                    'name',
                    'slug',
                    'warna',
                    'color',
                    'urutan',
                ],
            ],
            'total',
        ]);
        $response->assertJson(['status' => 'ok']);
    }

    public function test_login_page_renders_with_form(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Masuk ke Sistem');
        $response->assertSee('Superadmin (Pusat)');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'superadmin',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $response = $this->post('/login', [
            'username' => 'superadmin',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_admin_dashboard_requires_authentication(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
        $response->assertSee('Destinasi Aktif');
    }

    public function test_authenticated_admin_can_view_locations_list(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $response = $this->actingAs($user)->get(route('admin.locations.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Destinasi');
    }

    public function test_authenticated_admin_can_view_create_location_form(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $response = $this->actingAs($user)->get(route('admin.locations.create'));

        $response->assertStatus(200);
        $response->assertSee('Tambah Destinasi');
        $response->assertSee('pickMap');
    }

    public function test_authenticated_admin_can_store_new_location(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $category = Category::first();

        $response = $this->actingAs($user)->post(route('admin.locations.store'), [
            'nama' => 'Test Titik Wisata Galuh',
            'kategori_id' => $category->id,
            'kecamatan' => 'Ciamis',
            'latitude' => -7.3258,
            'longitude' => 108.3532,
            'alamat' => 'Alun-Alun Ciamis',
            'deskripsi' => 'Deskripsi testing otomasi Laravel.',
            'harga' => 'Gratis',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('admin.locations.index'));
        $this->assertDatabaseHas('locations', [
            'name' => 'Test Titik Wisata Galuh',
            'kecamatan' => 'Ciamis',
        ]);
    }

    public function test_authenticated_admin_can_view_categories_list(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $response = $this->actingAs($user)->get(route('admin.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Kategori Direktori');
    }

    public function test_regional_staff_cannot_edit_location_outside_assigned_district(): void
    {
        $staffPanjalu = User::where('username', 'staff_panjalu')->first();
        $ciamisLocation = Location::where('kecamatan', 'Ciamis')->first();

        $response = $this->actingAs($staffPanjalu)->get(route('admin.locations.edit', $ciamisLocation->id));

        $response->assertRedirect(route('admin.locations.index'));
        $response->assertSessionHas('error');
    }

    public function test_staff_kawali_can_edit_kawali_location_and_cannot_edit_cadas_ngampar(): void
    {
        $staffKawali = User::where('username', 'staff_kawali')->first();
        $kawaliLocation = Location::where('kecamatan', 'Kawali')->first();
        $cadasNgampar = Location::where('name', 'like', '%Cadas Ngampar%')->first();

        // 1. Staff Kawali CAN view edit form for Kawali destination
        $responseKawali = $this->actingAs($staffKawali)->get(route('admin.locations.edit', $kawaliLocation->id));
        $responseKawali->assertStatus(200);
        $responseKawali->assertSee('Edit Destinasi');
        $responseKawali->assertSee('pickMap');

        // 2. Staff Kawali CANNOT edit Cadas Ngampar (which is in Sadananya)
        $responseCadas = $this->actingAs($staffKawali)->get(route('admin.locations.edit', $cadasNgampar->id));
        $responseCadas->assertRedirect(route('admin.locations.index'));
        $responseCadas->assertSessionHas('error');

        // 3. Staff Kawali CANNOT update Cadas Ngampar via POST/PUT
        $responseUpdate = $this->actingAs($staffKawali)->put(route('admin.locations.update', $cadasNgampar->id), [
            'nama' => 'Illegal Edit Cadas Ngampar',
            'kategori_id' => $cadasNgampar->category_id,
            'latitude' => $cadasNgampar->latitude,
            'longitude' => $cadasNgampar->longitude,
        ]);
        $responseUpdate->assertRedirect(route('admin.locations.index'));
        $responseUpdate->assertSessionHas('error');
    }

    public function test_staff_sadananya_can_edit_and_update_cadas_ngampar(): void
    {
        $staffSadananya = User::where('username', 'staff_sadananya')->first();
        $cadasNgampar = Location::where('name', 'like', '%Cadas Ngampar%')->first();

        // 1. Staff Sadananya CAN access Cadas Ngampar edit form
        $responseForm = $this->actingAs($staffSadananya)->get(route('admin.locations.edit', $cadasNgampar->id));
        $responseForm->assertStatus(200);
        $responseForm->assertSee('Wisata Cadas Ngampar');

        // 2. Staff Sadananya CAN update name and drag/move coordinates within Sadananya
        $newLat = -7.248500;
        $newLng = 108.310200;
        $responseUpdate = $this->actingAs($staffSadananya)->put(route('admin.locations.update', $cadasNgampar->id), [
            'nama' => 'Wisata Sungai Cadas Ngampar Terverifikasi',
            'kategori_id' => $cadasNgampar->category_id,
            'latitude' => $newLat,
            'longitude' => $newLng,
            'alamat' => 'Desa Gunungsari, Kec. Sadananya, Kabupaten Ciamis',
            'deskripsi' => 'Aliran sungai pegunungan dengan bebatuan eksotis.',
            'harga' => 'Rp 5.000',
            'status' => 'aktif',
        ]);

        $responseUpdate->assertRedirect(route('admin.locations.index'));
        $responseUpdate->assertSessionHas('success');

        $this->assertDatabaseHas('locations', [
            'id' => $cadasNgampar->id,
            'name' => 'Wisata Sungai Cadas Ngampar Terverifikasi',
            'latitude' => $newLat,
            'longitude' => $newLng,
        ]);
    }

    public function test_regional_staff_cannot_move_pin_outside_wilayah_bounds(): void
    {
        $staffKawali = User::where('username', 'staff_kawali')->first();
        $kawaliLocation = Location::where('kecamatan', 'Kawali')->first();

        // Try to update coordinates to a point far outside Kawali (e.g. Ciamis center -7.326, 108.353)
        $response = $this->actingAs($staffKawali)->put(route('admin.locations.update', $kawaliLocation->id), [
            'nama' => $kawaliLocation->name,
            'kategori_id' => $kawaliLocation->category_id,
            'latitude' => -7.326694,
            'longitude' => 108.353192,
        ]);

        $response->assertSessionHasErrors('latitude');
    }

    public function test_user_can_logout(): void
    {
        $user = User::where('username', 'superadmin')->first();
        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect('/login?logout=1');
        $this->assertGuest();
    }
}
