<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\Role;
use Cheney\AdminSystem\Models\Permission;
use Illuminate\Pagination\LengthAwarePaginator;

class RoleService
{
    protected $roleModel;
    protected $permissionModel;

    public function __construct(Role $roleModel, Permission $permissionModel)
    {
        $this->roleModel = $roleModel;
        $this->permissionModel = $permissionModel;
    }

    /**
     * 获取角色列表
     * 
     * @param array $params 查询参数，支持 name（名称）、code（代码）、status（状态）、per_page（每页数量）
     * @return LengthAwarePaginator 返回分页的角色列表
     */
    public function index(array $params = []): LengthAwarePaginator
    {
        $query = $this->roleModel->query();

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['code'])) {
            $query->where('code', 'like', '%' . $params['code'] . '%');
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        $perPage = $params['per_page'] ?? 15;
        return $query->with('permissions')->orderBy('sort')->paginate($perPage);
    }

    /**
     * 获取角色详情
     * 
     * @param int $id 角色ID
     * @return Role 返回角色模型实例，包含权限关联数据
     */
    public function show(int $id)
    {
        return $this->roleModel->with('permissions')->findOrFail($id);
    }

    /**
     * 创建角色
     * 
     * @param array $data 角色数据，包含 name（名称）、code（代码）、description（描述）、sort（排序）、status（状态）等
     * @return Role 返回创建的角色模型实例
     */
    public function store(array $data): Role
    {
        return $this->roleModel->create($data);
    }

    /**
     * 更新角色
     * 
     * @param int $id 角色ID
     * @param array $data 角色数据，包含 name（名称）、code（代码）、description（描述）、sort（排序）、status（状态）等
     * @return Role 返回更新后的角色模型实例
     */
    public function update(int $id, array $data): Role
    {
        $role = $this->roleModel->findOrFail($id);
        $role->update($data);
        return $role->fresh();
    }

    /**
     * 删除角色
     * 
     * @param int $id 角色ID
     * @return bool 删除成功返回true，失败返回false
     * @throws \Exception 如果角色下有管理员，抛出异常
     */
    public function destroy(int $id): bool
    {
        $role = $this->roleModel->findOrFail($id);

        if ($role->admins()->exists()) {
            throw new \Exception('该角色下有管理员，无法删除');
        }

        return $role->delete();
    }

    /**
     * 为角色分配权限
     * 
     * @param int $roleId 角色ID
     * @param array $permissionIds 权限ID数组
     * @return void
     */
    public function assignPermissions(int $roleId, array $permissionIds): void
    {
        $role = $this->roleModel->findOrFail($roleId);
        $role->permissions()->sync($permissionIds);
    }

    /**
     * 为角色分配管理员
     * 
     * @param int $roleId 角色ID
     * @param array $adminIds 管理员ID数组
     * @return void
     */
    public function assignAdmins(int $roleId, array $adminIds): void
    {
        $role = $this->roleModel->findOrFail($roleId);
        $role->admins()->sync($adminIds);
    }
}
