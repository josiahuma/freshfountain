<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'pages.view',
            'pages.edit',
            'applications.view',
            'applications.edit',
            'users.manage',
            'settings.manage',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $clientAdmin = Role::firstOrCreate(['name' => 'client_admin', 'guard_name' => 'web']);

        $superAdmin->givePermissionTo(Permission::all());

        $clientAdmin->givePermissionTo([
            'pages.view',
            'pages.edit',
            'applications.view',
            'applications.edit',
        ]);
    }
}
