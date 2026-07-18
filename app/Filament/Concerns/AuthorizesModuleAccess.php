<?php

namespace App\Filament\Concerns;

use App\Support\Access\BackendAccess;
use Illuminate\Database\Eloquent\Model;

trait AuthorizesModuleAccess
{
    abstract protected static function permissionModule(): string;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }

    public static function canViewAny(): bool
    {
        return BackendAccess::canView(static::permissionModule());
    }

    public static function canView(Model $record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canEdit(Model $record): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canDelete(Model $record): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canDeleteAny(): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canForceDelete(Model $record): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canForceDeleteAny(): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canRestore(Model $record): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canRestoreAny(): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canReplicate(Model $record): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }

    public static function canReorder(): bool
    {
        return BackendAccess::canManage(static::permissionModule());
    }
}
