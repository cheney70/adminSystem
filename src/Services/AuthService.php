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

        $token = JWTAuth::fromUser($admin);

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

    public function logout()
    {
        auth('admin')->logout();
    }

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

    public function me()
    {
        $admin = auth('admin')->user();
        return $this->getUserInfo($admin);
    }

    public function updateProfile(array $data)
    {
        $admin = auth('admin')->user();
        $admin->update($data);
        return $this->getUserInfo($admin->fresh());
    }

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

    protected function getUserInfo($admin)
    {
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
