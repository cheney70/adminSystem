<?php

namespace Cheney\AdminSystem\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cheney\AdminSystem\Tests\TestCase;
use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\Role;

class RoleTest extends TestCase
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

    public function test_authenticated_admin_can_get_role_list()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/roles');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_create_role()
    {
        $token = auth('admin')->login($this->admin);
        
        $data = [
            'name' => 'New Role',
            'code' => 'new_role',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/roles', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('roles', [
            'name' => 'New Role',
            'code' => 'new_role',
        ]);
    }

    public function test_authenticated_admin_can_update_role()
    {
        $role = Role::factory()->create([
            'name' => 'Test Role',
            'code' => 'test_role',
            'status' => 1,
        ]);

        $token = auth('admin')->login($this->admin);
        
        $data = [
            'name' => 'Updated Role',
            'code' => 'test_role',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/system/roles/' . $role->id, $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('roles', [
            'id' => $role->id,
            'name' => 'Updated Role',
        ]);
    }

    public function test_authenticated_admin_can_delete_role()
    {
        $role = Role::factory()->create([
            'name' => 'Test Role',
            'code' => 'test_role',
            'status' => 1,
        ]);

        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/system/roles/' . $role->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('roles', [
            'id' => $role->id,
        ]);
    }

    public function test_authenticated_admin_can_assign_permissions_to_role()
    {
        $role = Role::factory()->create([
            'name' => 'Test Role',
            'code' => 'test_role',
            'status' => 1,
        ]);

        $permission = \Cheney\AdminSystem\Models\Permission::factory()->create([
            'name' => 'Test Permission',
            'code' => 'test_permission',
            'type' => 1,
        ]);

        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/roles/' . $role->id . '/permissions', [
            'permission_ids' => [$permission->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('permission_role', [
            'role_id' => $role->id,
            'permission_id' => $permission->id,
        ]);
    }
}
