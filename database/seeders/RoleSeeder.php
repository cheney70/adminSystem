<?php

namespace Database\Seeders;

use Cheney\AdminSystem\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        Role::create([
            'name' => '超级管理员',
            'code' => 'super_admin',
            'description' => '拥有所有权限',
            'sort' => 1,
            'status' => 1,
        ]);

        Role::create([
            'name' => '管理员',
            'code' => 'admin',
            'description' => '拥有大部分权限',
            'sort' => 2,
            'status' => 1,
        ]);

        Role::create([
            'name' => '编辑员',
            'code' => 'editor',
            'description' => '拥有编辑权限',
            'sort' => 3,
            'status' => 1,
        ]);

        Role::create([
            'name' => '普通用户',
            'code' => 'user',
            'description' => '只有查看权限',
            'sort' => 4,
            'status' => 1,
        ]);
    }
}
