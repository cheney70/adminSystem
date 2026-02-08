<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Services\UserService;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Get(
     *     path="/api/system/admins",
     *     summary="获取用户列表",
     *     description="获取系统用户列表，支持分页和搜索",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="页码",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="每页数量",
     *         required=false,
     *         @OA\Schema(type="integer", example=15)
     *     ),
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="搜索关键词",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="total", type="integer", example=100)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $admins = $this->userService->index($request->all());
            return $this->successPaginated($admins);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/admins",
     *     summary="创建用户",
     *     description="创建新用户",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password", "name"},
     *             @OA\Property(property="username", type="string", example="testuser", description="用户名"),
     *             @OA\Property(property="password", type="string", example="password123", description="密码，最少6位"),
     *             @OA\Property(property="name", type="string", example="测试用户", description="姓名"),
     *             @OA\Property(property="email", type="string", example="test@example.com", description="邮箱"),
     *             @OA\Property(property="phone", type="string", example="13800138000", description="手机号"),
     *             @OA\Property(property="avatar", type="string", example="http://example.com/avatar.jpg", description="头像URL"),
     *             @OA\Property(property="status", type="integer", example=1, description="状态：1-启用，0-禁用")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="创建成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="创建成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:50|unique:admins',
                'password' => 'required|string|min:6',
                'name' => 'required|string|max:50',
                'email' => 'nullable|email|max:100|unique:admins',
                'phone' => 'nullable|string|max:20',
                'avatar' => 'nullable|string',
                'status' => 'nullable|integer|in:0,1',
            ]);

            $admin = $this->userService->store($validated);
            return $this->created($admin);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/system/admins/{id}",
     *     summary="获取用户详情",
     *     description="获取指定用户的详细信息",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="用户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $admin = $this->userService->show($id);
            return $this->success($admin);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/system/admins/{id}",
     *     summary="更新用户",
     *     description="更新指定用户的信息",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="用户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="username", type="string", example="testuser"),
     *             @OA\Property(property="name", type="string", example="更新后的姓名"),
     *             @OA\Property(property="email", type="string", example="newemail@example.com"),
     *             @OA\Property(property="phone", type="string", example="13900139000"),
     *             @OA\Property(property="avatar", type="string", example="http://example.com/avatar.jpg"),
     *             @OA\Property(property="status", type="integer", example=1)
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
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|string|max:50|unique:admins,username,' . $id,
                'name' => 'required|string|max:50',
                'email' => 'nullable|email|max:100|unique:admins,email,' . $id,
                'phone' => 'nullable|string|max:20',
                'avatar' => 'nullable|string',
                'status' => 'nullable|integer|in:0,1',
            ]);

            $admin = $this->userService->update($id, $validated);
            return $this->success($admin, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/system/admins/{id}",
     *     summary="删除用户",
     *     description="删除指定用户（软删除）",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="用户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="删除成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="删除成功"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        try {
            $this->userService->destroy($id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/admins/{id}/roles",
     *     summary="分配角色",
     *     description="为用户分配角色",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="用户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"role_ids"},
     *             @OA\Property(
     *                 property="role_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3},
     *                 description="角色ID数组"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="分配成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="分配角色成功"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function assignRoles(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'role_ids' => 'required|array',
                'role_ids.*' => 'exists:roles,id',
            ]);

            $this->userService->assignRoles($id, $validated['role_ids']);
            return $this->success(null, '分配角色成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/admins/{id}/reset-password",
     *     summary="重置密码",
     *     description="重置指定用户的密码",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="用户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(property="password", type="string", example="newpassword123", description="新密码，最少6位")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="重置成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="重置密码成功"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'password' => 'required|string|min:6',
            ]);

            $this->userService->resetPassword($id, $validated['password']);
            return $this->success(null, '重置密码成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/admins/{id}/change-status",
     *     summary="修改状态",
     *     description="修改用户状态（启用/禁用）",
     *     tags={"用户管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="用户ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"status"},
     *             @OA\Property(property="status", type="integer", example=0, description="状态：1-启用，0-禁用")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="修改成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="状态修改成功"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function changeStatus(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'status' => 'required|integer|in:0,1',
            ]);

            $this->userService->changeStatus($id, $validated['status']);
            return $this->success(null, '状态修改成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
