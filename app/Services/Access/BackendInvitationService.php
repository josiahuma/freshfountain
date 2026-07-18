<?php

namespace App\Services\Access;

use App\Models\BackendInvitation;
use App\Models\User;
use App\Notifications\BackendAccessInvitationNotification;
use App\Services\MicrosoftGraphMailer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BackendInvitationService
{
    public function __construct(
        private readonly MicrosoftGraphMailer $mailer,
    ) {
    }

    public function send(User $user, bool $isResend = false): BackendInvitation
    {
        if (blank($user->email)) {
            throw new \RuntimeException('This backend account does not have an email address.');
        }

        [$invitation, $plainToken] = DB::transaction(
            function () use ($user): array {
                BackendInvitation::query()
                    ->where('user_id', $user->id)
                    ->whereNull('accepted_at')
                    ->update(['expires_at' => now()]);

                $plainToken = Str::random(64);

                $invitation = BackendInvitation::query()->create([
                    'user_id' => $user->id,
                    'token_hash' => hash('sha256', $plainToken),
                    'expires_at' => now()->addHours(24),
                    'sent_at' => null,
                ]);

                return [$invitation, $plainToken];
            }
        );

        $message = new BackendAccessInvitationNotification(
            $plainToken,
            $isResend,
        );

        try {
            $this->mailer->send(
                $user->email,
                $message->subject(),
                $message->html($user),
            );

            $invitation->forceFill([
                'sent_at' => now(),
            ])->save();
        } catch (\Throwable $exception) {
            $invitation->forceFill([
                'expires_at' => now(),
            ])->save();

            report($exception);

            throw $exception;
        }

        return $invitation->fresh();
    }

    public function cancel(User $user): int
    {
        return BackendInvitation::query()
            ->where('user_id', $user->id)
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);
    }

    public function findUsable(string $plainToken): ?BackendInvitation
    {
        return BackendInvitation::query()
            ->with('user')
            ->where('token_hash', hash('sha256', $plainToken))
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    public function accept(
        BackendInvitation $invitation,
        string $password
    ): User {
        return DB::transaction(function () use ($invitation, $password): User {
            $user = $invitation->user;

            $user->forceFill([
                'password' => $password,
                'email_verified_at' => $user->email_verified_at ?? now(),
                'has_backend_access' => true,
            ])->save();

            $invitation->forceFill([
                'accepted_at' => now(),
            ])->save();

            BackendInvitation::query()
                ->where('user_id', $user->id)
                ->whereKeyNot($invitation->id)
                ->whereNull('accepted_at')
                ->update(['expires_at' => now()]);

            return $user->fresh();
        });
    }
}
