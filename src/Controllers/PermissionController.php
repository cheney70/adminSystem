<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Services\PermissionService;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

class PermissionController extends Controller
{
    use ApiResponseTrait;

    protected $permissionService;

    public function __construct(PermissionService $permissionService)
    {
        $this->permissionService = $permissionService;
    }

    /**
     * @OA\Get(
     *     path="/api/system/permissions",
     *     summary="获取权限列表",
     *     description="获取系统权限列表，支持分页和搜索",
     *     tags={"权限管理"},
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
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="权限类型：1-菜单权限，2-按钮权限",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
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
     *                 @OA\Property(property="total", type="integer", example=50)
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $permissions = $this->permissionService->index($request->all());
            return $this->successPaginated($permissions);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/permissions",
     *     summary="创建权限",
     *     description="创建新权限",
     *     tags={"权限管理"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code", "type"},
     *             @OA\Property(property="name", type="string", example="用户管理", description="权限名称"),
     *             @OA\Property(property="code", type="string", example="user.manage", description="权限编码"),
     *             @OA\Property(property="description", type="string", example="用户管理权限", description="权限描述"),
     *             @OA\Property(property="menu_id", type="integer", example=1, description="关联菜单ID"),
     *             @OA\Property(property="type", type="integer", example=1, description="权限类型：1-菜单权限，2-按钮权限")
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
                'name' => 'required|string|max:50',
                'code' => 'required|string|max:50|unique:permissions',
                'description' => 'nullable|string|max:255',
                'menu_id' => 'nullable|integer|exists:menus,id',
                'type' => 'required|integer|in:1,2',
            ]);

            $permission = $this->permissionService->store($validated);
            return $this->created($permission);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/system/permissions/{id}",
     *     summary="获取权限详情",
     *     description="获取指定权限的详细信息",
     *     tags={"权限管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="权限ID",
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
            $permission = $this->permissionService->show($id);
            return $this->success($permission);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/system/permissions/{id}",
     *     summary="更新权限",
     *     description="更新指定权限的信息",
     *     tags={"权限管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="权限ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="用户管理（更新）"),
     *             @OA\Property(property="code", type="string", example="user.manage"),
     *             @OA\Property(property="description", type="string", example="用户管理权限（更新）"),
     *             @OA\Property(property="menu_id", type="integer", example=1),
     *             @OA\Property(property="type", type="integer", example=1)
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
                'name' => 'required|string|max:50',
                'code' => 'required|string|max:50|unique:permissions,code,' . $id,
                'description' => 'nullable|string|max:255',
                'menu_id' => 'nullable|integer|exists:menus,id',
                'type' => 'required|integer|in:1,2',
            ]);

            $permission = $this->permissionService->update($id, $validated);
            return $this->success($permission, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/system/permissions/{id}",
     *     summary="删除权限",
     *     description="删除指定权限",
     *     tags={"权限管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="权限ID",
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
            $this->permissionService->destroy($id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
