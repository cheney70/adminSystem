<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Services\AuthService;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    use ApiResponseTrait;

    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *     path="/api/system/auth/login",
     *     summary="用户登录",
     *     description="使用用户名和密码登录系统",
     *     tags={"认证"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="admin", description="用户名"),
     *             @OA\Property(property="password", type="string", example="password123", description="密码")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="登录成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="登录成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=86400),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="username", type="string", example="admin"),
     *                     @OA\Property(property="name", type="string", example="超级管理员")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            $result = $this->authService->login($validated);

            return $this->success($result, '登录成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/auth/logout",
     *     summary="退出登录",
     *     description="退出当前登录状态",
     *     tags={"认证"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="退出成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="退出成功"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function logout()
    {
        try {
            $this->authService->logout();
            return $this->success(null, '退出成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/auth/refresh",
     *     summary="刷新Token",
     *     description="刷新当前访问令牌",
     *     tags={"认证"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="刷新成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="刷新成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="token_type", type="string", example="bearer"),
     *                 @OA\Property(property="expires_in", type="integer", example=86400)
     *             )
     *         )
     *     )
     * )
     */
    public function refresh()
    {
        try {
            $token = $this->authService->refresh();
            return $this->success($token, '刷新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/system/auth/me",
     *     summary="获取当前用户信息",
     *     description="获取当前登录用户的详细信息",
     *     tags={"认证"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="username", type="string", example="admin"),
     *                 @OA\Property(property="name", type="string", example="超级管理员"),
     *                 @OA\Property(property="email", type="string", example="admin@example.com"),
     *                 @OA\Property(property="phone", type="string", example="13800138000"),
     *                 @OA\Property(property="avatar", type="string", nullable=true),
     *                 @OA\Property(property="status", type="integer", example=1),
     *                 @OA\Property(
     *                     property="roles",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="超级管理员")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="permissions",
     *                     type="array",
     *                     @OA\Items(type="string", example="user.manage")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function me()
    {
        try {
            $admin = $this->authService->me();
            return $this->success($admin);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/system/auth/profile",
     *     summary="更新个人信息",
     *     description="更新当前登录用户的个人信息",
     *     tags={"认证"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="更新后的姓名"),
     *             @OA\Property(property="email", type="string", example="newemail@example.com"),
     *             @OA\Property(property="phone", type="string", example="13900139000"),
     *             @OA\Property(property="avatar", type="string", example="http://example.com/avatar.jpg")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="更新成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="更新成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function updateProfile(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:50',
                'email' => 'nullable|email|max:100',
                'phone' => 'nullable|string|max:20',
                'avatar' => 'nullable|string',
            ]);

            $admin = $this->authService->updateProfile($validated);
            return $this->success($admin, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/auth/change-password",
     *     summary="修改密码",
     *     description="修改当前登录用户的密码",
     *     tags={"认证"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"old_password", "new_password"},
     *             @OA\Property(property="old_password", type="string", example="oldpassword123", description="旧密码"),
     *             @OA\Property(property="new_password", type="string", example="newpassword123", description="新密码，最少6位"),
     *             @OA\Property(property="new_password_confirmation", type="string", example="newpassword123", description="确认密码")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="密码修改成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="密码修改成功"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request)
    {
        try {
            $validated = $request->validate([
                'old_password' => 'required|string',
                'new_password' => 'required|string|min:6|confirmed',
            ]);

            $this->authService->changePassword($validated);
            return $this->success(null, '密码修改成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
