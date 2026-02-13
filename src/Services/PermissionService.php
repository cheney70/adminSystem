<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;

class PermissionService
{
    protected $permissionModel;

    public function __construct(Permission $permissionModel)
    {
        $this->permissionModel = $permissionModel;
    }

    /**
     * 获取权限列表
     * 
     * @param array $params 查询参数，支持 name（名称）、code（代码）、type（类型）、menu_id（菜单ID）、per_page（每页数量）
     * @return LengthAwarePaginator 返回分页的权限列表
     */
    public function index(array $params = []): LengthAwarePaginator
    {
        $query = $this->permissionModel->query();

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['code'])) {
            $query->where('code', 'like', '%' . $params['code'] . '%');
        }

        if (isset($params['type'])) {
            $query->where('type', $params['type']);
        }

        if (isset($params['menu_id'])) {
            $query->where('menu_id', $params['menu_id']);
        }

        $perPage = $params['per_page'] ?? 15;
        return $query->with('menu')->orderBy('id')->paginate($perPage);
    }

    /**
     * 获取权限详情
     * 
     * @param int $id 权限ID
     * @return Permission 返回权限模型实例，包含菜单和角色关联数据
     */
    public function show(int $id)
    {
        return $this->permissionModel->with('menu', 'roles')->findOrFail($id);
    }

    /**
     * 创建权限
     * 
     * @param array $data 权限数据，包含 name（名称）、code（代码）、description（描述）、menu_id（菜单ID）、type（类型）等
     * @return Permission 返回创建的权限模型实例
     */
    public function store(array $data): Permission
    {
        return $this->permissionModel->create($data);
    }

    /**
     * 更新权限
     * 
     * @param int $id 权限ID
     * @param array $data 权限数据，包含 name（名称）、code（代码）、description（描述）、menu_id（菜单ID）、type（类型）等
     * @return Permission 返回更新后的权限模型实例
     */
    public function update(int $id, array $data): Permission
    {
        $permission = $this->permissionModel->findOrFail($id);
        $permission->update($data);
        return $permission->fresh();
    }

    /**
     * 删除权限
     * 
     * @param int $id 权限ID
     * @return bool 删除成功返回true，失败返回false
     * @throws \Exception 如果权限已被角色使用，抛出异常
     */
    public function destroy(int $id): bool
    {
        $permission = $this->permissionModel->findOrFail($id);

        if ($permission->roles()->exists()) {
            throw new \Exception('该权限已被角色使用，无法删除');
        }

        return $permission->delete();
    }
}
