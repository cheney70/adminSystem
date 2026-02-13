<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSystemDatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            RoleSeeder::class,
            MenuSeeder::class,
            PermissionSeeder::class,
            RoleAdminSeeder::class,
            PermissionRoleSeeder::class,
        ]);
    }
}
