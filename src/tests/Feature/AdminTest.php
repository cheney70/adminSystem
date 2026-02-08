<?php

namespace Cheney\AdminSystem\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cheney\AdminSystem\Tests\TestCase;
use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\Role;

class AdminTest extends TestCase
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

    public function test_authenticated_admin_can_get_admin_list()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/admins');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_create_admin()
    {
        $token = auth('admin')->login($this->admin);
        
        $data = [
            'username' => 'newadmin',
            'password' => 'password123',
            'name' => 'New Admin',
            'email' => 'newadmin@example.com',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/admins', $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('admins', [
            'username' => 'newadmin',
            'name' => 'New Admin',
        ]);
    }

    public function test_authenticated_admin_can_update_admin()
    {
        $admin = Admin::factory()->create([
            'username' => 'updateadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $token = auth('admin')->login($this->admin);
        
        $data = [
            'username' => 'updateadmin',
            'name' => 'Updated Admin',
            'email' => 'updated@example.com',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/system/admins/' . $admin->id, $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('admins', [
            'id' => $admin->id,
            'name' => 'Updated Admin',
        ]);
    }

    public function test_authenticated_admin_can_delete_admin()
    {
        $admin = Admin::factory()->create([
            'username' => 'deleteadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->delete('/api/system/admins/' . $admin->id);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('admins', [
            'id' => $admin->id,
        ]);
    }

    public function test_authenticated_admin_can_assign_roles_to_admin()
    {
        $admin = Admin::factory()->create([
            'username' => 'roleadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $role = Role::factory()->create([
            'name' => 'Test Role',
            'code' => 'test_role',
            'status' => 1,
        ]);

        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/admins/' . $admin->id . '/roles', [
            'role_ids' => [$role->id],
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('role_admin', [
            'admin_id' => $admin->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_authenticated_admin_can_reset_password()
    {
        $admin = Admin::factory()->create([
            'username' => 'passwordadmin',
            'password' => bcrypt('oldpassword'),
            'status' => 1,
        ]);

        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/admins/' . $admin->id . '/reset-password', [
            'password' => 'newpassword',
        ]);

        $response->assertStatus(200);
        $admin->refresh();
        $this->assertTrue(\Hash::check('newpassword', $admin->password));
    }
}
