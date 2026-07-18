<?php

namespace App\Support\Access;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class BackendAccess
{
    public static function user(): ?User
    {
        $user = Auth::user();

        return $user instanceof User ? $user : null;
    }

    public static function isSuperAdmin(?User $user = null): bool
    {
        $user ??= self::user();

        return $user !== null
            && ($user->is_admin || $user->hasRole('super-admin'));
    }

    public static function canView(string $module, ?User $user = null): bool
    {
        $user ??= self::user();

        if (! $user) {
            return false;
        }

        return self::isSuperAdmin($user)
            || $user->can(BackendPermissions::view($module))
            || $user->can(BackendPermissions::manage($module));
    }

    public static function canManage(string $module, ?User $user = null): bool
    {
        $user ??= self::user();

        return $user !== null
            && (self::isSuperAdmin($user)
                || $user->can(BackendPermissions::manage($module)));
    }

    public static function canViewDonorIdentities(?User $user = null): bool
    {
        $user ??= self::user();

        return $user !== null
            && (self::isSuperAdmin($user)
                || $user->can(BackendPermissions::VIEW_DONOR_IDENTITIES));
    }
}
