<?php

namespace Cheney\AdminSystem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'sort',
        'status',
    ];

    protected $casts = [
        'status' => 'integer',
        'sort' => 'integer',
    ];

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'role_admin', 'role_id', 'admin_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role');
    }

    public function hasPermission($permissionCode)
    {
        return $this->permissions()->where('code', $permissionCode)->exists();
    }

    public function hasAnyPermission(array $permissionCodes)
    {
        return $this->permissions()->whereIn('code', $permissionCodes)->exists();
    }

    public function hasAllPermissions(array $permissionCodes)
    {
        $permissionCount = $this->permissions()->whereIn('code', $permissionCodes)->count();
        return $permissionCount === count($permissionCodes);
    }
}
