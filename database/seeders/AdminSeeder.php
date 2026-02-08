<?php

namespace Database\Seeders;

use Cheney\AdminSystem\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        Admin::create([
            'username' => 'admin',
            'password' => bcrypt('123456'),
            'name' => '超级管理员',
            'email' => 'admin@example.com',
            'phone' => '13800138000',
            'status' => 1,
        ]);

        Admin::create([
            'username' => 'editor',
            'password' => bcrypt('123456'),
            'name' => '编辑员',
            'email' => 'editor@example.com',
            'phone' => '13800138001',
            'status' => 1,
        ]);

        Admin::create([
            'username' => 'user',
            'password' => bcrypt('123456'),
            'name' => '普通用户',
            'email' => 'user@example.com',
            'phone' => '13800138002',
            'status' => 1,
        ]);
    }
}
