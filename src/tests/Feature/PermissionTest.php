<?php

namespace Cheney\AdminSystem\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cheney\AdminSystem\Tests\TestCase;
use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\Permission;

class PermissionTest extends TestCase
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

    public function test_authenticated_admin_can_get_permission_list()
    {
        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/permissions');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_create_permission()
    {
        $token = auth('api')->login($this->admin);
        
        $data = [
            'name' => 'New Permission',
            'code' => 'new_permission',
            'type' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/permissions', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('permissions', [
            'name' => 'New Permission',
            'code' => 'new_permission',
        ]);
    }

    public function test_authenticated_admin_can_update_permission()
    {
        $permission = Permission::factory()->create([
            'name' => 'Test Permission',
            'code' => 'test_permission',
            'type' => 1,
        ]);

        $token = auth('api')->login($this->admin);
        
        $data = [
            'name' => 'Updated Permission',
            'code' => 'test_permission',
            'type' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/system/permissions/' . $permission->id, $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'Updated Permission',
        ]);
    }

    public function test_authenticated_admin_can_delete_permission()
    {
        $permission = Permission::factory()->create([
            'name' => 'Test Permission',
            'code' => 'test_permission',
            'type' => 1,
        ]);

        $token = auth('api')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/system/permissions/' . $permission->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('permissions', [
            'id' => $permission->id,
        ]);
    }
}
