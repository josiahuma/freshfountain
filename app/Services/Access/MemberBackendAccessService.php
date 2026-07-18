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
    public function __construct(
        private readonly BackendInvitationService $invitations,
    ) {
    }

    public function sync(
        Member $member,
        bool $enabled,
        array $permissions
    ): ?User {
        if ($enabled && blank($member->email)) {
            throw ValidationException::withMessages([
                'email' => 'An email address is required before backend access can be enabled.',
            ]);
        }

        [$user, $created] = DB::transaction(
            function () use ($member, $enabled, $permissions): array {
                $user = $member->user;
                $created = false;

                if (! $user && filled($member->email)) {
                    $user = User::query()
                        ->whereRaw('LOWER(email) = ?', [strtolower($member->email)])
                        ->first();
                }

                if (! $enabled && ! $user) {
                    return [null, false];
                }

                if (! $user) {
                    $user = User::query()->create([
                        'name' => $member->display_name ?: $member->full_name,
                        'email' => strtolower($member->email),
                        'password' => Hash::make(Str::random(64)),
                        'is_admin' => false,
                        'has_backend_access' => true,
                    ]);
                    $created = true;
                } else {
                    $user->forceFill([
                        'name' => $member->display_name ?: $member->full_name,
                        'email' => strtolower($member->email ?: $user->email),
                        'has_backend_access' => $enabled || $user->is_admin,
                    ])->save();
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

                return [$user->fresh(), $created];
            }
        );

        if ($created && $user) {
            $this->invitations->send($user);
        }

        return $user;
    }

    public function resendInvitation(Member $member): void
    {
        $user = $member->user;

        if (! $user || ! $user->has_backend_access) {
            throw ValidationException::withMessages([
                'backend_access_enabled' => 'Enable backend access before sending an invitation.',
            ]);
        }

        $this->invitations->send($user, true);
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
