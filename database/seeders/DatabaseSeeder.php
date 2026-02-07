<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Create roles + permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // 2) Create (or get) the admin user
        $admin = User::firstOrCreate(
            ['email' => 'webmaster@freshfountain.org'],
            [
                'name' => 'Admin',
                'password' => Hash::make('3wf38&8Vu0fgXx!Swcs&'),
            ]
        );

        // 3) Assign super admin role
        $admin->assignRole('super_admin');

        // Optional test user (remove if you don’t want it)
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
