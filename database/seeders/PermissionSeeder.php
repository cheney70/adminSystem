<?php

namespace Database\Seeders;

use Cheney\AdminSystem\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        Permission::create([
            'name' => '管理员列表',
            'code' => 'admin:list',
            'description' => '查看管理员列表',
            'menu_id' => 2,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '创建管理员',
            'code' => 'admin:create',
            'description' => '创建管理员',
            'menu_id' => 2,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '编辑管理员',
            'code' => 'admin:update',
            'description' => '编辑管理员',
            'menu_id' => 2,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '删除管理员',
            'code' => 'admin:delete',
            'description' => '删除管理员',
            'menu_id' => 2,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '角色列表',
            'code' => 'role:list',
            'description' => '查看角色列表',
            'menu_id' => 3,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '创建角色',
            'code' => 'role:create',
            'description' => '创建角色',
            'menu_id' => 3,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '编辑角色',
            'code' => 'role:update',
            'description' => '编辑角色',
            'menu_id' => 3,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '删除角色',
            'code' => 'role:delete',
            'description' => '删除角色',
            'menu_id' => 3,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '权限列表',
            'code' => 'permission:list',
            'description' => '查看权限列表',
            'menu_id' => 4,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '创建权限',
            'code' => 'permission:create',
            'description' => '创建权限',
            'menu_id' => 4,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '编辑权限',
            'code' => 'permission:update',
            'description' => '编辑权限',
            'menu_id' => 4,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '删除权限',
            'code' => 'permission:delete',
            'description' => '删除权限',
            'menu_id' => 4,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '菜单列表',
            'code' => 'menu:list',
            'description' => '查看菜单列表',
            'menu_id' => 5,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '创建菜单',
            'code' => 'menu:create',
            'description' => '创建菜单',
            'menu_id' => 5,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '编辑菜单',
            'code' => 'menu:update',
            'description' => '编辑菜单',
            'menu_id' => 5,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '删除菜单',
            'code' => 'menu:delete',
            'description' => '删除菜单',
            'menu_id' => 5,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '日志列表',
            'code' => 'log:list',
            'description' => '查看日志列表',
            'menu_id' => 6,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '删除日志',
            'code' => 'log:delete',
            'description' => '删除日志',
            'menu_id' => 6,
            'type' => 2,
        ]);

        // 文章管理权限
        Permission::create([
            'name' => '文章列表',
            'code' => 'article:list',
            'description' => '查看文章列表',
            'menu_id' => 7,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '创建文章',
            'code' => 'article:create',
            'description' => '创建文章',
            'menu_id' => 7,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '编辑文章',
            'code' => 'article:update',
            'description' => '编辑文章',
            'menu_id' => 7,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '删除文章',
            'code' => 'article:delete',
            'description' => '删除文章',
            'menu_id' => 7,
            'type' => 2,
        ]);

        // 分类管理权限
        Permission::create([
            'name' => '分类列表',
            'code' => 'category:list',
            'description' => '查看分类列表',
            'menu_id' => 8,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '创建分类',
            'code' => 'category:create',
            'description' => '创建分类',
            'menu_id' => 8,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '编辑分类',
            'code' => 'category:update',
            'description' => '编辑分类',
            'menu_id' => 8,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '删除分类',
            'code' => 'category:delete',
            'description' => '删除分类',
            'menu_id' => 8,
            'type' => 2,
        ]);

        // 标签管理权限
        Permission::create([
            'name' => '标签列表',
            'code' => 'tag:list',
            'description' => '查看标签列表',
            'menu_id' => 9,
            'type' => 1,
        ]);

        Permission::create([
            'name' => '创建标签',
            'code' => 'tag:create',
            'description' => '创建标签',
            'menu_id' => 9,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '编辑标签',
            'code' => 'tag:update',
            'description' => '编辑标签',
            'menu_id' => 9,
            'type' => 2,
        ]);

        Permission::create([
            'name' => '删除标签',
            'code' => 'tag:delete',
            'description' => '删除标签',
            'menu_id' => 9,
            'type' => 2,
        ]);
    }
}
