<?php

namespace Database\Seeders;

use Cheney\AdminSystem\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        Menu::create([
            'title' => '系统管理',
            'name' => 'System',
            'parent_id' => 0,
            'path' => '/system',
            'component' => 'Layout',
            'icon' => 'setting',
            'type' => 1,
            'sort' => 1,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '管理员管理',
            'name' => 'Admin',
            'parent_id' => 1,
            'path' => '/system/admin',
            'component' => 'system/admin/index',
            'icon' => 'user',
            'type' => 2,
            'sort' => 1,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '角色管理',
            'name' => 'Role',
            'parent_id' => 1,
            'path' => '/system/role',
            'component' => 'system/role/index',
            'icon' => 'team',
            'type' => 2,
            'sort' => 2,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '权限管理',
            'name' => 'Permission',
            'parent_id' => 1,
            'path' => '/system/permission',
            'component' => 'system/permission/index',
            'icon' => 'safety',
            'type' => 2,
            'sort' => 3,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '菜单管理',
            'name' => 'Menu',
            'parent_id' => 1,
            'path' => '/system/menu',
            'component' => 'system/menu/index',
            'icon' => 'menu',
            'type' => 2,
            'sort' => 4,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '操作日志',
            'name' => 'OperationLog',
            'parent_id' => 1,
            'path' => '/system/operation-log',
            'component' => 'system/operation-log/index',
            'icon' => 'file-text',
            'type' => 2,
            'sort' => 5,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        // 文章管理菜单
        Menu::create([
            'title' => '文章管理',
            'name' => 'Article',
            'parent_id' => 0,
            'path' => '/article',
            'component' => 'Layout',
            'icon' => 'file-text',
            'type' => 1,
            'sort' => 2,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '文章列表',
            'name' => 'ArticleList',
            'parent_id' => 7,
            'path' => '/article/index',
            'component' => 'article/index',
            'icon' => 'file',
            'type' => 2,
            'sort' => 1,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '分类管理',
            'name' => 'CategoryList',
            'parent_id' => 7,
            'path' => '/article/categories',
            'component' => 'article/categories/index',
            'icon' => 'folder',
            'type' => 2,
            'sort' => 2,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);

        Menu::create([
            'title' => '标签管理',
            'name' => 'TagList',
            'parent_id' => 7,
            'path' => '/article/tags',
            'component' => 'article/tags/index',
            'icon' => 'tag',
            'type' => 2,
            'sort' => 3,
            'status' => 1,
            'is_hidden' => false,
            'keep_alive' => true,
        ]);
    }
}
