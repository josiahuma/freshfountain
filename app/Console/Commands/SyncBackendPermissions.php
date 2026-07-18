<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Access\BackendPermissions;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SyncBackendPermissions extends Command
{
    protected $signature = 'permissions:sync-backend';

    protected $description = 'Create Fresh Fountain backend permissions and preserve existing super administrators.';

    public function handle(): int
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (BackendPermissions::all() as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $role = Role::findOrCreate('super-admin', 'web');
        $role->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

        User::query()->where('is_admin', true)->each(function (User $user) use ($role): void {
            $user->forceFill(['has_backend_access' => true])->save();
            $user->assignRole($role);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $this->info('Backend permissions synchronised.');

        return self::SUCCESS;
    }
}
