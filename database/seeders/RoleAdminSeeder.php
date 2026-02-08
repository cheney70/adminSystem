<?php

namespace Cheney\AdminSystem\Database\Seeders;

use Cheney\AdminSystem\Models\Admin;
use Cheney\AdminSystem\Models\Role;
use Illuminate\Database\Seeder;

class RoleAdminSeeder extends Seeder
{
    public function run()
    {
        $adminAdmin = Admin::where('username', 'admin')->first();
        $editorAdmin = Admin::where('username', 'editor')->first();
        $normalAdmin = Admin::where('username', 'user')->first();

        $superAdminRole = Role::where('code', 'super_admin')->first();
        $adminRole = Role::where('code', 'admin')->first();
        $editorRole = Role::where('code', 'editor')->first();
        $userRole = Role::where('code', 'user')->first();

        $adminAdmin->roles()->attach($superAdminRole->id);
        $editorAdmin->roles()->attach($editorRole->id);
        $normalAdmin->roles()->attach($userRole->id);
    }
}
