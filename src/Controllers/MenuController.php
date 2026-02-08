<?php

namespace Cheney\AdminSystem\Controllers;

use Cheney\AdminSystem\Controllers\Controller;
use Illuminate\Http\Request;
use Cheney\AdminSystem\Services\MenuService;
use Cheney\AdminSystem\Traits\ApiResponseTrait;
use OpenApi\Annotations as OA;

class MenuController extends Controller
{
    use ApiResponseTrait;

    protected $menuService;

    public function __construct(MenuService $menuService)
    {
        $this->menuService = $menuService;
    }

    /**
     * @OA\Get(
     *     path="/api/system/menus",
     *     summary="获取菜单列表",
     *     description="获取系统菜单列表，支持树形结构",
     *     tags={"菜单管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="搜索关键词",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="状态：1-启用，0-禁用",
     *         required=false,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        try {
            $menus = $this->menuService->index($request->all());
            return $this->success($menus);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/system/menus",
     *     summary="创建菜单",
     *     description="创建新菜单",
     *     tags={"菜单管理"},
     *     security={{"bearer":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "type"},
     *             @OA\Property(property="name", type="string", example="用户管理", description="菜单名称"),
     *             @OA\Property(property="code", type="string", example="user", description="菜单编码"),
     *             @OA\Property(property="path", type="string", example="/user", description="路由路径"),
     *             @OA\Property(property="component", type="string", example="user/index", description="组件路径"),
     *             @OA\Property(property="icon", type="string", example="user", description="菜单图标"),
     *             @OA\Property(property="parent_id", type="integer", example=0, description="父菜单ID，0表示顶级菜单"),
     *             @OA\Property(property="sort", type="integer", example=1, description="排序"),
     *             @OA\Property(property="type", type="integer", example=1, description="菜单类型：1-目录，2-菜单，3-按钮"),
     *             @OA\Property(property="status", type="integer", example=1, description="状态：1-启用，0-禁用"),
     *             @OA\Property(property="visible", type="integer", example=1, description="是否显示：1-显示，0-隐藏")
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
                'code' => 'nullable|string|max:50',
                'path' => 'nullable|string|max:255',
                'component' => 'nullable|string|max:255',
                'icon' => 'nullable|string|max:50',
                'parent_id' => 'nullable|integer|min:0',
                'sort' => 'nullable|integer|min:0',
                'type' => 'required|integer|in:1,2,3',
                'status' => 'nullable|integer|in:0,1',
                'visible' => 'nullable|integer|in:0,1',
            ]);

            $menu = $this->menuService->store($validated);
            return $this->created($menu);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/system/menus/{id}",
     *     summary="获取菜单详情",
     *     description="获取指定菜单的详细信息",
     *     tags={"菜单管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="菜单ID",
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
            $menu = $this->menuService->show($id);
            return $this->success($menu);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Put(
     *     path="/api/system/menus/{id}",
     *     summary="更新菜单",
     *     description="更新指定菜单的信息",
     *     tags={"菜单管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="菜单ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="用户管理（更新）"),
     *             @OA\Property(property="code", type="string", example="user"),
     *             @OA\Property(property="path", type="string", example="/user"),
     *             @OA\Property(property="component", type="string", example="user/index"),
     *             @OA\Property(property="icon", type="string", example="user"),
     *             @OA\Property(property="parent_id", type="integer", example=0),
     *             @OA\Property(property="sort", type="integer", example=1),
     *             @OA\Property(property="type", type="integer", example=1),
     *             @OA\Property(property="status", type="integer", example=1),
     *             @OA\Property(property="visible", type="integer", example=1)
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
                'code' => 'nullable|string|max:50',
                'path' => 'nullable|string|max:255',
                'component' => 'nullable|string|max:255',
                'icon' => 'nullable|string|max:50',
                'parent_id' => 'nullable|integer|min:0',
                'sort' => 'nullable|integer|min:0',
                'type' => 'required|integer|in:1,2,3',
                'status' => 'nullable|integer|in:0,1',
                'visible' => 'nullable|integer|in:0,1',
            ]);

            $menu = $this->menuService->update($id, $validated);
            return $this->success($menu, '更新成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/system/menus/{id}",
     *     summary="删除菜单",
     *     description="删除指定菜单",
     *     tags={"菜单管理"},
     *     security={{"bearer":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="菜单ID",
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
            $this->menuService->destroy($id);
            return $this->success(null, '删除成功');
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/system/menus/tree",
     *     summary="获取菜单树",
     *     description="获取菜单树形结构",
     *     tags={"菜单管理"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function tree()
    {
        try {
            $tree = $this->menuService->tree();
            return $this->success($tree);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/system/menus/user",
     *     summary="获取当前用户菜单",
     *     description="获取当前登录用户的菜单列表",
     *     tags={"菜单管理"},
     *     security={{"bearer":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="获取成功",
     *         @OA\JsonContent(
     *             @OA\Property(property="code", type="integer", example=10000),
     *             @OA\Property(property="message", type="string", example="获取成功"),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function userMenus()
    {
        try {
            $menus = $this->menuService->userMenus();
            return $this->success($menus);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}
