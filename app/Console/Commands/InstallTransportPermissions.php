<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Access\BackendPermissions;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class InstallTransportPermissions extends Command
{
    protected $signature = 'transport:install-permissions';

    protected $description = 'Create Transport module permissions using Fresh Fountain backend permission naming.';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            BackendPermissions::view('transport'),
            BackendPermissions::manage('transport'),
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role = Role::findOrCreate('super-admin', 'web');
        $role->givePermissionTo($permissions);

        User::query()
            ->where('is_admin', true)
            ->each(function (User $user) use ($role): void {
                $user->forceFill(['has_backend_access' => true])->save();
                $user->assignRole($role);
            });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Transport backend permissions installed.');
        $this->line('View: ' . BackendPermissions::view('transport'));
        $this->line('Manage: ' . BackendPermissions::manage('transport'));

        return self::SUCCESS;
    }
}
