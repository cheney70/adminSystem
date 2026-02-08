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

    public function show(int $id)
    {
        return $this->adminModel->with('roles')->findOrFail($id);
    }

    public function store(array $data): Admin
    {
        $data['password'] = bcrypt($data['password']);
        return $this->adminModel->create($data);
    }

    public function update(int $id, array $data): Admin
    {
        $admin = $this->adminModel->findOrFail($id);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $admin->update($data);
        return $admin->fresh();
    }

    public function destroy(int $id): bool
    {
        $admin = $this->adminModel->findOrFail($id);

        $currentAdmin = auth('admin')->user();
        if ($admin->id === $currentAdmin->id) {
            throw new \Exception('不能删除当前登录用户');
        }

        return $admin->delete();
    }

    public function assignRoles(int $adminId, array $roleIds): void
    {
        $admin = $this->adminModel->findOrFail($adminId);
        $admin->roles()->sync($roleIds);
    }

    public function resetPassword(int $adminId, string $password): void
    {
        $admin = $this->adminModel->findOrFail($adminId);
        $admin->update([
            'password' => bcrypt($password),
        ]);
    }

    public function changeStatus(int $adminId, int $status): void
    {
        $admin = $this->adminModel->findOrFail($adminId);
        $admin->update(['status' => $status]);
    }
}
