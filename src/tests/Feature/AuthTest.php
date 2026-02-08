<?php

namespace Cheney\AdminSystem\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cheney\AdminSystem\Tests\TestCase;
use Cheney\AdminSystem\Models\Admin;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_valid_credentials()
    {
        $admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'code',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'user',
            ],
        ]);
    }

    public function test_admin_cannot_login_with_invalid_credentials()
    {
        $admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    public function test_admin_cannot_login_with_disabled_account()
    {
        $admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password'),
            'status' => 0,
        ]);

        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    public function test_authenticated_admin_can_logout()
    {
        $admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $token = auth('api')->login($admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/auth/logout');

        $response->assertStatus(200);
    }

    public function test_authenticated_admin_can_get_profile()
    {
        $admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password'),
            'status' => 1,
        ]);

        $token = auth('api')->login($admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/auth/me');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'code',
            'message',
            'data' => [
                'id',
                'username',
                'name',
                'email',
                'phone',
                'avatar',
                'roles',
                'permissions',
            ],
        ]);
    }
}
