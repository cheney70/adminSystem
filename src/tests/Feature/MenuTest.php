<?php

namespace Cheney\AdminSystem\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cheney\AdminSystem\Tests\TestCase;
use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\Menu;

class MenuTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);
    }

    public function test_authenticated_admin_can_get_menu_list()
    {
        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/menus');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_create_menu()
    {
        $token = auth('api')->login($this->admin);
        
        $data = [
            'title' => 'New Menu',
            'name' => 'NewMenu',
            'type' => 2,
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/menus', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('menus', [
            'title' => 'New Menu',
            'name' => 'NewMenu',
        ]);
    }

    public function test_authenticated_admin_can_update_menu()
    {
        $menu = Menu::factory()->create([
            'title' => 'Test Menu',
            'name' => 'TestMenu',
            'type' => 2,
            'status' => 1,
        ]);

        $token = auth('api')->login($this->admin);
        
        $data = [
            'title' => 'Updated Menu',
            'name' => 'TestMenu',
            'type' => 2,
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/system/menus/' . $menu->id, $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'title' => 'Updated Menu',
        ]);
    }

    public function test_authenticated_admin_can_delete_menu()
    {
        $menu = Menu::factory()->create([
            'title' => 'Test Menu',
            'name' => 'TestMenu',
            'type' => 2,
            'status' => 1,
        ]);

        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/system/menus/' . $menu->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('menus', [
            'id' => $menu->id,
        ]);
    }

    public function test_authenticated_admin_can_get_menu_tree()
    {
        $menu = Menu::factory()->create([
            'title' => 'Parent Menu',
            'name' => 'ParentMenu',
            'parent_id' => 0,
            'type' => 1,
            'status' => 1,
        ]);

        Menu::factory()->create([
            'title' => 'Child Menu',
            'name' => 'ChildMenu',
            'parent_id' => $menu->id,
            'type' => 2,
            'status' => 1,
        ]);

        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/menus/tree');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_get_user_menus()
    {
        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/user-menus');

        $response->assertStatus(200);
    }
}
