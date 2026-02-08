<?php

namespace Cheney\AdminSystem\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Cheney\AdminSystem\Tests\TestCase;
use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\Role;
use Cheney\AdminSystem\Models\Permission;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = Admin::factory()->create([
            'username' => 'testadmin',
            'password' => bcrypt('password123'),
            'name' => '测试管理员',
            'email' => 'test@example.com',
            'phone' => '13800138000',
            'status' => 1,
        ]);
    }

    /**
     * 测试管理员可以使用有效凭证登录
     */
    public function test_admin_can_login_with_valid_credentials()
    {
        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 10000,
            'message' => '登录成功',
        ]);
        
        $response->assertJsonStructure([
            'code',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
                'user' => [
                    'id',
                    'username',
                    'name',
                    'email',
                    'phone',
                    'avatar',
                    'roles',
                    'permissions',
                ],
            ],
        ]);
        
        $data = $response->json('data');
        $this->assertEquals('bearer', $data['token_type']);
        $this->assertIsString($data['access_token']);
        $this->assertIsInt($data['expires_in']);
        $this->assertEquals('testadmin', $data['user']['username']);
        $this->assertEquals('测试管理员', $data['user']['name']);
    }

    /**
     * 测试管理员无法使用错误密码登录
     */
    public function test_admin_cannot_login_with_invalid_password()
    {
        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
        $this->assertStringContainsString('密码错误', $response->json('message'));
    }

    /**
     * 测试不存在的用户名无法登录
     */
    public function test_admin_cannot_login_with_nonexistent_username()
    {
        $response = $this->post('/api/system/auth/login', [
            'username' => 'nonexistent',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
        $this->assertStringContainsString('用户不存在', $response->json('message'));
    }

    /**
     * 测试禁用的账号无法登录
     */
    public function test_admin_cannot_login_with_disabled_account()
    {
        $this->admin->update(['status' => 0]);

        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
        $this->assertStringContainsString('账号已被禁用', $response->json('message'));
    }

    /**
     * 测试登录时缺少必填字段
     */
    public function test_login_requires_username_and_password()
    {
        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
        ]);

        $response->assertStatus(422);
        
        $response = $this->post('/api/system/auth/login', [
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    /**
     * 测试已认证的管理员可以退出登录
     */
    public function test_authenticated_admin_can_logout()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 10000,
            'message' => '退出成功',
        ]);
    }

    /**
     * 测试未认证的管理员无法退出登录
     */
    public function test_unauthenticated_admin_cannot_logout()
    {
        $response = $this->post('/api/system/auth/logout');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    /**
     * 测试已认证的管理员可以刷新 token
     */
    public function test_authenticated_admin_can_refresh_token()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/auth/refresh');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 10000,
            'message' => '刷新成功',
        ]);
        
        $response->assertJsonStructure([
            'code',
            'message',
            'data' => [
                'access_token',
                'token_type',
                'expires_in',
            ],
        ]);
        
        $data = $response->json('data');
        $this->assertIsString($data['access_token']);
        $this->assertEquals('bearer', $data['token_type']);
        $this->assertIsInt($data['expires_in']);
        
        $newToken = $data['access_token'];
        $this->assertNotEquals($token, $newToken);
    }

    /**
     * 测试未认证的管理员无法刷新 token
     */
    public function test_unauthenticated_admin_cannot_refresh_token()
    {
        $response = $this->post('/api/system/auth/refresh');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    /**
     * 测试已认证的管理员可以获取个人信息
     */
    public function test_authenticated_admin_can_get_profile()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/auth/me');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 10000,
            'message' => '获取成功',
        ]);
        
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
                'created_at',
            ],
        ]);
        
        $data = $response->json('data');
        $this->assertEquals($this->admin->id, $data['id']);
        $this->assertEquals('testadmin', $data['username']);
        $this->assertEquals('测试管理员', $data['name']);
        $this->assertEquals('test@example.com', $data['email']);
        $this->assertEquals('13800138000', $data['phone']);
        $this->assertIsArray($data['roles']);
        $this->assertIsArray($data['permissions']);
    }

    /**
     * 测试未认证的管理员无法获取个人信息
     */
    public function test_unauthenticated_admin_cannot_get_profile()
    {
        $response = $this->get('/api/system/auth/me');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    /**
     * 测试已认证的管理员可以更新个人信息
     */
    public function test_authenticated_admin_can_update_profile()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->put('/api/system/auth/profile', [
            'name' => '更新后的管理员',
            'email' => 'updated@example.com',
            'phone' => '13900139000',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 10000,
            'message' => '更新成功',
        ]);
        
        $this->assertDatabaseHas('admins', [
            'id' => $this->admin->id,
            'name' => '更新后的管理员',
            'email' => 'updated@example.com',
            'phone' => '13900139000',
        ]);
    }

    /**
     * 测试未认证的管理员无法更新个人信息
     */
    public function test_unauthenticated_admin_cannot_update_profile()
    {
        $response = $this->put('/api/system/auth/profile', [
            'name' => '更新后的管理员',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    /**
     * 测试已认证的管理员可以修改密码
     */
    public function test_authenticated_admin_can_change_password()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/auth/change-password', [
            'old_password' => 'password123',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 10000,
            'message' => '修改成功',
        ]);
        
        $this->admin->refresh();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword456', $this->admin->password));
    }

    /**
     * 测试使用错误的旧密码无法修改密码
     */
    public function test_admin_cannot_change_password_with_wrong_old_password()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/auth/change-password', [
            'old_password' => 'wrongpassword',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
        $this->assertStringContainsString('原密码错误', $response->json('message'));
    }

    /**
     * 测试未认证的管理员无法修改密码
     */
    public function test_unauthenticated_admin_cannot_change_password()
    {
        $response = $this->post('/api/system/auth/change-password', [
            'old_password' => 'password123',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'newpassword456',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    /**
     * 测试修改密码时密码确认不匹配
     */
    public function test_password_confirmation_must_match()
    {
        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->post('/api/system/auth/change-password', [
            'old_password' => 'password123',
            'new_password' => 'newpassword456',
            'new_password_confirmation' => 'differentpassword',
        ]);

        $response->assertStatus(422);
    }

    /**
     * 测试获取的管理员信息包含正确的角色和权限
     */
    public function test_admin_profile_includes_roles_and_permissions()
    {
        $role = Role::factory()->create(['name' => '测试角色']);
        $permission = Permission::factory()->create(['code' => 'test.permission']);
        
        $role->permissions()->attach($permission->id);
        $this->admin->roles()->attach($role->id);

        $token = auth('admin')->login($this->admin);
        
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->get('/api/system/auth/me');

        $response->assertStatus(200);
        
        $data = $response->json('data');
        $this->assertContains('测试角色', $data['roles']);
        $this->assertContains('test.permission', $data['permissions']);
    }

    /**
     * 测试登录后更新最后登录时间和IP
     */
    public function test_login_updates_last_login_info()
    {
        $this->assertNull($this->admin->last_login_at);
        $this->assertNull($this->admin->last_login_ip);

        $response = $this->post('/api/system/auth/login', [
            'username' => 'testadmin',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        
        $this->admin->refresh();
        $this->assertNotNull($this->admin->last_login_at);
        $this->assertNotNull($this->admin->last_login_ip);
    }

    /**
     * 测试使用无效的 token 访问受保护的路由
     */
    public function test_invalid_token_cannot_access_protected_routes()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token',
        ])->get('/api/system/auth/me');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }

    /**
     * 测试不提供 token 访问受保护的路由
     */
    public function test_no_token_cannot_access_protected_routes()
    {
        $response = $this->get('/api/system/auth/me');

        $response->assertStatus(200);
        $response->assertJson([
            'code' => 20000,
        ]);
    }
}
