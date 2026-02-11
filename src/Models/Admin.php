<?php

namespace Cheney\AdminSystem\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

/**
 * Admin Model
 * 
 * 管理员模型，用于系统后台管理员的身份认证和权限管理
 * 支持JWT认证，通过角色关联实现基于RBAC的权限控制
 * 
 * @package Cheney\AdminSystem\Models
 */
class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable, HasFactory, SoftDeletes;

    /**
     * 关联的数据表名称
     * 
     * @var string
     */
    protected $table = 'admins';

    /**
     * 可批量赋值的属性
     * 
     * @var array
     */
    protected $fillable = [
        'username',      // 登录用户名
        'password',      // 加密密码
        'name',          // 真实姓名
        'email',         // 邮箱地址
        'phone',         // 手机号码
        'avatar',        // 头像路径
        'status',        // 状态：1启用，0禁用
        'last_login_at', // 最后登录时间
        'last_login_ip', // 最后登录IP
    ];

    /**
     * 序列化时隐藏的敏感属性
     * 
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 属性类型转换
     * 
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'status' => 'integer',
    ];

    /**
     * 获取JWT标识符
     * 用于JWT认证时识别用户身份
     * 
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * 获取JWT自定义声明
     * 可在JWT payload中添加额外的自定义数据
     * 
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * 管理员与角色的多对多关联
     * 一个管理员可以拥有多个角色
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_admin', 'admin_id', 'role_id');
    }

    /**
     * 管理员与操作日志的一对多关联
     * 记录管理员的所有操作历史
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function operationLogs()
    {
        return $this->hasMany(OperationLog::class, 'admin_id');
    }

    /**
     * 检查管理员是否拥有指定权限
     * 通过遍历所有角色来判断是否拥有该权限
     * 
     * @param string $permissionCode 权限代码
     * @return bool
     */
    public function hasPermission($permissionCode)
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permissionCode)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查管理员是否拥有任意一个指定权限
     * 只要拥有权限列表中的任意一个即返回true
     * 
     * @param array $permissionCodes 权限代码数组
     * @return bool
     */
    public function hasAnyPermission(array $permissionCodes)
    {
        foreach ($this->roles as $role) {
            if ($role->hasAnyPermission($permissionCodes)) {
                return true;
            }
        }
        return false;
    }

    /**
     * 检查管理员是否拥有所有指定权限
     * 必须同时拥有权限列表中的所有权限才返回true
     * 
     * @param array $permissionCodes 权限代码数组
     * @return bool
     */
    public function hasAllPermissions(array $permissionCodes)
    {
        foreach ($this->roles as $role) {
            if ($role->hasAllPermissions($permissionCodes)) {
                return true;
            }
        }
        return false;
    }
}
