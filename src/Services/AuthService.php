<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\Admin;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Cheney\AdminSystem\Exceptions\AuthException;

class AuthService
{
    protected $adminModel;

    public function __construct(Admin $adminModel)
    {
        $this->adminModel = $adminModel;
    }

    /**
     * 管理员登录
     * 
     * @param array $credentials 登录凭证，包含 username（用户名）和 password（密码）
     * @return array 返回包含 token 和用户信息的数组
     * @throws AuthException 如果用户不存在、密码错误或账号被禁用，抛出异常
     */
    public function login(array $credentials)
    {
        $username = $credentials['username'];
        $password = $credentials['password'];

        $admin = $this->adminModel->where('username', $username)->first();

        if (!$admin) {
            throw new AuthException('用户不存在');
        }

        if ($admin->status != 1) {
            throw new AuthException('账号已被禁用');
        }

        if (!Hash::check($password, $admin->password)) {
            throw new AuthException('密码错误');
        }

        // 使用 admin guard 生成 token
        $token = auth('admin')->fromUser($admin);

        $admin->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60,
            'user' => $this->getUserInfo($admin),
        ];
    }

    /**
     * 管理员登出
     * 
     * @return void
     */
    public function logout()
    {
        auth('admin')->logout();
    }

    /**
     * 刷新 token
     * 
     * @return array 返回包含新 token 的数组
     */
    public function refresh()
    {
        $token = auth('admin')->refresh();
        $admin = auth('admin')->user();

        return [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('admin')->factory()->getTTL() * 60,
        ];
    }

    /**
     * 获取当前登录用户信息
     * 
     * @return array 返回用户信息数组，包含角色和权限
     */
    public function me()
    {
        $admin = auth('admin')->user();
        return $this->getUserInfo($admin);
    }

    /**
     * 更新用户资料
     * 
     * @param array $data 用户数据，包含 name（姓名）、email（邮箱）、phone（手机号）、avatar（头像）等
     * @return array 返回更新后的用户信息
     */
    public function updateProfile(array $data)
    {
        $admin = auth('admin')->user();
        $admin->update($data);
        return $this->getUserInfo($admin->fresh());
    }

    /**
     * 修改密码
     * 
     * @param array $data 密码数据，包含 old_password（原密码）和 new_password（新密码）
     * @return void
     * @throws AuthException 如果原密码错误，抛出异常
     */
    public function changePassword(array $data)
    {
        $admin = auth('admin')->user();
        
        if (!Hash::check($data['old_password'], $admin->password)) {
            throw new AuthException('原密码错误');
        }

        $admin->update([
            'password' => bcrypt($data['new_password']),
        ]);
    }

    /**
     * 获取用户信息
     * 
     * @param Admin $admin 管理员模型实例
     * @return array 返回用户信息数组，包含角色和权限
     */
    protected function getUserInfo($admin)
    {
        // 预加载角色和权限关联
        $admin = $admin->load(['roles', 'roles.permissions']);
        
        $permissions = [];
        foreach ($admin->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission->code;
            }
        }
        
        $permissions = array_unique($permissions);

        return [
            'id' => $admin->id,
            'username' => $admin->username,
            'name' => $admin->name,
            'email' => $admin->email,
            'phone' => $admin->phone,
            'avatar' => $admin->avatar,
            'roles' => $admin->roles->pluck('name'),
            'permissions' => array_values($permissions),
            'created_at' => $admin->created_at,
        ];
    }
}
