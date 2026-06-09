<?php

namespace Tests\Feature;

use App\Models\AddOn;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddonCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::create([
            'username' => 'admin_test',
            'password' => 'password123',
        ]);
    }

    public function test_admin_can_view_addons_list(): void
    {
        AddOn::create(['name' => 'Bakso', 'price' => 5000]);

        $response = $this->actingAs($this->admin)->get(route('addons.index'));

        $response->assertStatus(200);
        $response->assertSee('Bakso');
        $response->assertSee('5000');
    }

    public function test_admin_can_create_addon(): void
    {
        $response = $this->actingAs($this->admin)->post(route('addons.store'), [
            'name' => 'Pangsit Goreng',
            'price' => 3000,
            'description' => 'Pangsit renyah gurih'
        ]);

        $response->assertRedirect(route('addons.index'));
        $this->assertDatabaseHas('add_ons', [
            'name' => 'Pangsit Goreng',
            'price' => 3000,
            'description' => 'Pangsit renyah gurih'
        ]);
    }

    public function test_admin_can_update_addon(): void
    {
        $addon = AddOn::create(['name' => 'Ceker', 'price' => 4000]);

        $response = $this->actingAs($this->admin)->put(route('addons.update', $addon->id), [
            'name' => 'Ceker Pedas',
            'price' => 6000,
            'description' => 'Ceker bumbu mercon',
            'is_available' => '1'
        ]);

        $response->assertRedirect(route('addons.index'));
        $this->assertDatabaseHas('add_ons', [
            'id' => $addon->id,
            'name' => 'Ceker Pedas',
            'price' => 6000,
            'description' => 'Ceker bumbu mercon',
            'is_available' => true
        ]);
    }

    public function test_admin_can_delete_addon(): void
    {
        $addon = AddOn::create(['name' => 'Sayap', 'price' => 7000]);

        $response = $this->actingAs($this->admin)->delete(route('addons.destroy', $addon->id));

        $response->assertRedirect(route('addons.index'));
        $this->assertDatabaseMissing('add_ons', ['id' => $addon->id]);
    }

    public function test_create_addon_validation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('addons.store'), [
            'name' => '', // Required
            'price' => -100 // Min 0
        ]);

        $response->assertSessionHasErrors(['name', 'price']);
    }
}
