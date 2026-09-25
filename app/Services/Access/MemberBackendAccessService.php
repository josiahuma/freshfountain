<?php

namespace App\Services\Access;

use App\Models\Member;
use App\Models\User;
use App\Support\Access\BackendPermissions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class MemberBackendAccessService
{
    public function sync(
        Member $member,
        bool $enabled,
        array $permissions,
        ?string $plainPassword = null,
    ): ?User {
        $loginEmail = filled($member->email)
            ? strtolower(trim($member->email))
            : null;

        if ($enabled && blank($loginEmail)) {
            throw ValidationException::withMessages([
                'email' => 'An email address is required before /hub access can be enabled.',
            ]);
        }

        return DB::transaction(function () use (
            $member,
            $enabled,
            $permissions,
            $plainPassword,
            $loginEmail
        ): ?User {
            $user = $member->user;

            if (! $user && filled($loginEmail)) {
                $user = User::query()
                    ->whereRaw('LOWER(email) = ?', [$loginEmail])
                    ->first();
            }

            if (! $enabled && ! $user) {
                return null;
            }

            if (! $user) {
                $user = User::query()->create([
                    'name' => $member->display_name ?: $member->full_name,
                    'email' => $loginEmail,
                    'password' => filled($plainPassword)
                        ? Hash::make($plainPassword)
                        : Hash::make(Str::random(64)),
                    'is_admin' => false,
                    'has_backend_access' => true,
                ]);
            } else {
                if (
                    filled($loginEmail)
                    && User::query()
                        ->whereRaw('LOWER(email) = ?', [$loginEmail])
                        ->whereKeyNot($user->id)
                        ->exists()
                ) {
                    throw ValidationException::withMessages([
                        'email' => 'That email address is already used by another login account.',
                    ]);
                }

                $changes = [
                    'name' => $member->display_name ?: $member->full_name,
                    'email' => $loginEmail,
                    'has_backend_access' => $enabled || $user->is_admin,
                ];

                if (filled($plainPassword)) {
                    $changes['password'] = Hash::make($plainPassword);
                }

                $user->forceFill($changes)->save();
            }

            if ((int) $member->user_id !== (int) $user->id) {
                $member->forceFill(['user_id' => $user->id])->saveQuietly();
            }

            $allowed = collect($permissions)
                ->filter(fn (mixed $permission): bool =>
                    is_string($permission)
                    && in_array($permission, BackendPermissions::all(), true)
                )
                ->unique()
                ->values();

            foreach ($allowed as $permission) {
                Permission::findOrCreate($permission, 'web');
            }

            if (! $user->is_admin && ! $user->hasRole('super-admin')) {
                $user->syncPermissions($enabled ? $allowed->all() : []);
            }

            return $user->fresh();
        });
    }

    public function deactivate(Member $member): void
    {
        $user = $member->user;

        if (! $user || $user->is_admin || $user->hasRole('super-admin')) {
            return;
        }

        $user->forceFill(['has_backend_access' => false])->save();
        $user->syncPermissions([]);
    }
}
