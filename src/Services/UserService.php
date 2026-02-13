<?php

namespace Cheney\AdminSystem\Services;

use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    protected $adminModel;
    protected $roleModel;

    public function __construct(Admin $adminModel, Role $roleModel)
    {
        $this->adminModel = $adminModel;
        $this->roleModel = $roleModel;
    }

    /**
     * 获取管理员列表
     * 
     * @param array $params 查询参数，支持 username（用户名）、name（姓名）、email（邮箱）、status（状态）、per_page（每页数量）
     * @return LengthAwarePaginator 返回分页的管理员列表
     */
    public function index(array $params = []): LengthAwarePaginator
    {
        $query = $this->adminModel->query();

        if (isset($params['username'])) {
            $query->where('username', 'like', '%' . $params['username'] . '%');
        }

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        if (isset($params['email'])) {
            $query->where('email', 'like', '%' . $params['email'] . '%');
        }

        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        $perPage = $params['per_page'] ?? 15;
        return $query->with('roles')->orderBy('id', 'desc')->paginate($perPage);
    }

    /**
     * 获取管理员详情
     * 
     * @param int $id 管理员ID
     * @return Admin 返回管理员模型实例，包含角色关联数据
     */
    public function show(int $id)
    {
        return $this->adminModel->with('roles')->findOrFail($id);
    }

    /**
     * 创建管理员
     * 
     * @param array $data 管理员数据，包含 username（用户名）、password（密码）、name（姓名）、email（邮箱）、phone（手机号）等
     * @return Admin 返回创建的管理员模型实例
     */
    public function store(array $data): Admin
    {
        $data['password'] = bcrypt($data['password']);
        return $this->adminModel->create($data);
    }

    /**
     * 更新管理员
     * 
     * @param int $id 管理员ID
     * @param array $data 管理员数据，包含 username（用户名）、password（密码）、name（姓名）、email（邮箱）、phone（手机号）等
     * @return Admin 返回更新后的管理员模型实例
     */
    public function update(int $id, array $data): Admin
    {
        $admin = $this->adminModel->findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $admin->update($data);
        return $admin->fresh();
    }

    /**
     * 删除管理员
     * 
     * @param int $id 管理员ID
     * @return bool 删除成功返回true，失败返回false
     * @throws \Exception 如果删除的是当前登录用户，抛出异常
     */
    public function destroy(int $id): bool
    {
        $admin = $this->adminModel->findOrFail($id);

        $currentAdmin = auth('admin')->user();
        if ($admin->id === $currentAdmin->id) {
            throw new \Exception('不能删除当前登录用户');
        }

        return $admin->delete();
    }

    /**
     * 为管理员分配角色
     * 
     * @param int $adminId 管理员ID
     * @param array $roleIds 角色ID数组
     * @return void
     */
    public function assignRoles(int $adminId, array $roleIds): void
    {
        $admin = $this->adminModel->findOrFail($adminId);
        $admin->roles()->sync($roleIds);
    }

    /**
     * 重置管理员密码
     * 
     * @param int $adminId 管理员ID
     * @param string $password 新密码
     * @return void
     */
    public function resetPassword(int $adminId, string $password): void
    {
        $admin = $this->adminModel->findOrFail($adminId);
        $admin->update([
            'password' => bcrypt($password),
        ]);
    }

    /**
     * 修改管理员状态
     * 
     * @param int $adminId 管理员ID
     * @param int $status 状态：1-启用，0-禁用
     * @return void
     */
    public function changeStatus(int $adminId, int $status): void
    {
        $admin = $this->adminModel->findOrFail($adminId);
        $admin->update(['status' => $status]);
    }
}
