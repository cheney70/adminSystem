<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\Menu;
use Cheney\AdminSystem\Models\Permission;
use Tymon\JWTAuth\Facades\JWTAuth;

class MenuService
{
    protected $menuModel;
    protected $permissionModel;

    public function __construct(Menu $menuModel, Permission $permissionModel)
    {
        $this->menuModel = $menuModel;
        $this->permissionModel = $permissionModel;
    }

    /**
     * 获取菜单列表
     * 
     * @param array $params 查询参数，支持 title（标题）和 status（状态）
     * @return array 返回树形结构的菜单列表
     */
    public function index(array $params = [])
    {
        $query = $this->menuModel->query();

        if (isset($params['title'])) {
            $query->where('title', 'like', '%' . $params['title'] . '%');
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        $menus = $query->orderBy('sort')->get();
        
        return $this->buildTree($menus->toArray());
    }

    /**
     * 获取菜单详情
     * 
     * @param int $id 菜单ID
     * @return Menu 返回菜单模型实例
     */
    public function show(int $id)
    {
        return $this->menuModel->findOrFail($id);
    }

    /**
     * 创建菜单
     * 
     * @param array $data 菜单数据
     * @return Menu 返回创建的菜单模型实例
     */
    public function store(array $data): Menu
    {
        return $this->menuModel->create($data);
    }

    /**
     * 更新菜单
     * 
     * @param int $id 菜单ID
     * @param array $data 菜单数据
     * @return Menu 返回更新后的菜单模型实例
     */
    public function update(int $id, array $data): Menu
    {
        $menu = $this->menuModel->findOrFail($id);
        $menu->update($data);
        return $menu->fresh();
    }

    /**
     * 删除菜单
     * 
     * @param int $id 菜单ID
     * @return bool 删除成功返回true，失败返回false
     * @throws \Exception 如果菜单下有子菜单，抛出异常
     */
    public function destroy(int $id): bool
    {
        $menu = $this->menuModel->findOrFail($id);

        if ($menu->children()->exists()) {
            throw new \Exception('该菜单下有子菜单，无法删除');
        }

        return $menu->delete();
    }

    /**
     * 获取菜单树
     * 
     * @return array 返回树形结构的菜单列表
     */
    public function tree()
    {
        $menus = $this->menuModel->orderBy('sort')->get();
        return $this->buildTree($menus->toArray());
    }

    /**
     * 获取当前用户的菜单列表
     * 
     * @param mixed $admin 管理员用户对象，如果为null则从认证中获取
     * @return array 返回树形结构的用户菜单列表
     * @throws \Exception 如果用户未登录或token已过期，抛出异常
     */
    public function userMenus($admin = null)
    {
        if ($admin === null) {
            $admin = auth('admin')->user();
        }
        
        if (!$admin) {
            throw new \Exception('用户未登录或token已过期');
        }
        
        // 使用关联关系直接获取用户有权限的菜单ID
        $menuIds = $this->permissionModel->whereHas('roles', function ($query) use ($admin) {
            $query->whereHas('admins', function ($query) use ($admin) {
                $query->where('admins.id', $admin->id);
            });
        })
        ->whereNotNull('menu_id')
        ->pluck('menu_id')
        ->unique()
        ->filter();
        
        // 获取所有菜单（包括父菜单）
        $menus = $this->menuModel->where(function ($query) use ($menuIds) {
            // 直接有权限的菜单
            $query->whereIn('id', $menuIds)
                  // 或者是有权限菜单的父菜单
                  ->orWhereIn('id', function ($subQuery) use ($menuIds) {
                      $subQuery->select('parent_id')
                               ->from('menus')
                               ->whereIn('id', $menuIds)
                               ->where('parent_id', '>', 0);
                  });
        })
        ->active()
        ->notHidden()
        ->orderBy('sort')
        ->get();
        
        // 使用 Eloquent 关联关系构建树形结构
        return $this->buildTreeWithRelations($menus);
    }
    
    /**
     * 使用 Eloquent 关联关系构建菜单树
     * 
     * @param mixed $menus 菜单集合
     * @return array 返回树形结构的菜单列表
     */
    protected function buildTreeWithRelations($menus)
    {
        // 构建菜单ID到菜单对象的映射
        $menuMap = $menus->keyBy('id');
        
        // 使用 filter 和 map 方法构建树形结构
        return $menuMap->filter(function ($menu) {
            return $menu->parent_id == 0;
        })->map(function ($menu) use ($menuMap) {
            return $this->buildMenuTree($menu, $menuMap);
        })->values()->toArray();
    }
    
    /**
     * 递归构建菜单树
     * 
     * @param Menu $menu 当前菜单对象
     * @param mixed $menuMap 菜单ID到菜单对象的映射
     * @return array 返回包含子菜单的菜单数组
     */
    protected function buildMenuTree($menu, $menuMap)
    {
        $menuArray = $menu->toArray();
        
        // 使用 Eloquent 关联关系获取子菜单
        $children = $menuMap->filter(function ($child) use ($menu) {
            return $child->parent_id == $menu->id;
        });
        
        if ($children->isNotEmpty()) {
            // 使用 map 方法递归构建子菜单
            $menuArray['children'] = $children->map(function ($child) use ($menuMap) {
                return $this->buildMenuTree($child, $menuMap);
            })->values()->toArray();
        }
        
        return $menuArray;
    }
}
