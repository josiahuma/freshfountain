<?php

namespace App\Services\Notifications;

use App\Filament\Resources\UnitMembershipRequests\UnitMembershipRequestResource;
use App\Models\Leader;
use App\Models\UnitMembershipRequest;
use App\Services\MicrosoftGraphMailer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Throwable;

class UnitMembershipNotificationService
{
    public function __construct(
        private readonly MicrosoftGraphMailer $mailer
    ) {
    }

    public function requestSubmitted(
        UnitMembershipRequest $request
    ): void {
        $request->loadMissing([
            'churchUnit',
            'assignedLeader',
            'member',
        ]);

        if (filled($request->email)) {
            $this->sendSafely(
                $request->email,
                "We received your request to join {$request->churchUnit->name}",
                'emails.unit-membership.request-submitted-applicant',
                [
                    'request' => $request,
                ],
                'request_submitted_applicant',
                $request
            );
        }

        $operationalRecipients =
            $this->operationalRecipients(
                $request
            );

        foreach (
            $operationalRecipients
            as $recipient
        ) {
            $this->sendSafely(
                $recipient['email'],
                "New request to join {$request->churchUnit->name}",
                'emails.unit-membership.request-submitted-team',
                [
                    'request' =>
                        $request,

                    'recipientName' =>
                        $recipient['name'],

                    'adminUrl' =>
                        $this->adminUrl(
                            $request
                        ),
                ],
                'request_submitted_team',
                $request
            );
        }
    }

    public function leaderAssigned(
        UnitMembershipRequest $request
    ): void {
        $request->loadMissing([
            'churchUnit',
            'assignedLeader',
            'member',
        ]);

        $recipients = [];

        if (
            $request->assignedLeader
            && filled(
                $request
                    ->assignedLeader
                    ->email
            )
        ) {
            $recipients[] = [
                'email' =>
                    $request
                        ->assignedLeader
                        ->email,

                'name' =>
                    $request
                        ->assignedLeader
                        ->display_name,
            ];
        }

        if (
            $request->churchUnit
            && filled(
                $request
                    ->churchUnit
                    ->email
            )
        ) {
            $recipients[] = [
                'email' =>
                    $request
                        ->churchUnit
                        ->email,

                'name' =>
                    $request
                        ->churchUnit
                        ->name,
            ];
        }

        foreach (
            $this->uniqueRecipients(
                $recipients
            )
            as $recipient
        ) {
            $this->sendSafely(
                $recipient['email'],
                "Leader assigned to {$request->display_name}'s unit request",
                'emails.unit-membership.leader-assigned',
                [
                    'request' =>
                        $request,

                    'recipientName' =>
                        $recipient['name'],

                    'adminUrl' =>
                        $this->adminUrl(
                            $request
                        ),
                ],
                'leader_assigned',
                $request
            );
        }
    }

    public function requestApproved(
        UnitMembershipRequest $request
    ): void {
        $request->loadMissing([
            'churchUnit',
            'assignedLeader',
            'member',
        ]);

        if (filled($request->email)) {
            $this->sendSafely(
                $request->email,
                "Welcome to {$request->churchUnit->name}",
                'emails.unit-membership.request-approved-applicant',
                [
                    'request' => $request,
                ],
                'request_approved_applicant',
                $request
            );
        }

        foreach (
            $this->operationalRecipients(
                $request
            )
            as $recipient
        ) {
            $this->sendSafely(
                $recipient['email'],
                "{$request->display_name}'s request has been approved",
                'emails.unit-membership.request-approved-team',
                [
                    'request' =>
                        $request,

                    'recipientName' =>
                        $recipient['name'],

                    'adminUrl' =>
                        $this->adminUrl(
                            $request
                        ),
                ],
                'request_approved_team',
                $request
            );
        }
    }

    public function requestDeclined(
        UnitMembershipRequest $request
    ): void {
        $request->loadMissing([
            'churchUnit',
            'assignedLeader',
            'member',
        ]);

        if (filled($request->email)) {
            $this->sendSafely(
                $request->email,
                "Update on your request to join {$request->churchUnit->name}",
                'emails.unit-membership.request-declined-applicant',
                [
                    'request' => $request,
                ],
                'request_declined_applicant',
                $request
            );
        }

        if (
            $request->churchUnit
            && filled(
                $request
                    ->churchUnit
                    ->email
            )
        ) {
            $this->sendSafely(
                $request
                    ->churchUnit
                    ->email,
                "{$request->display_name}'s unit request has been declined",
                'emails.unit-membership.request-declined-team',
                [
                    'request' =>
                        $request,

                    'recipientName' =>
                        $request
                            ->churchUnit
                            ->name,

                    'adminUrl' =>
                        $this->adminUrl(
                            $request
                        ),
                ],
                'request_declined_team',
                $request
            );
        }
    }

    public function requestCompleted(
        UnitMembershipRequest $request
    ): void {
        $request->loadMissing([
            'churchUnit',
            'assignedLeader',
            'member',
        ]);

        if (filled($request->email)) {
            $this->sendSafely(
                $request->email,
                "Your membership with {$request->churchUnit->name}",
                'emails.unit-membership.request-completed-applicant',
                [
                    'request' => $request,
                ],
                'request_completed_applicant',
                $request
            );
        }

        if (
            $request->assignedLeader
            && filled(
                $request
                    ->assignedLeader
                    ->email
            )
        ) {
            $this->sendSafely(
                $request
                    ->assignedLeader
                    ->email,
                "{$request->display_name}'s unit request is complete",
                'emails.unit-membership.request-completed-leader',
                [
                    'request' =>
                        $request,

                    'recipientName' =>
                        $request
                            ->assignedLeader
                            ->display_name,

                    'adminUrl' =>
                        $this->adminUrl(
                            $request
                        ),
                ],
                'request_completed_leader',
                $request
            );
        }
    }

    private function operationalRecipients(
        UnitMembershipRequest $request
    ): array {
        $recipients = [];

        if (
            $request->assignedLeader
            && filled(
                $request
                    ->assignedLeader
                    ->email
            )
        ) {
            $recipients[] = [
                'email' =>
                    $request
                        ->assignedLeader
                        ->email,

                'name' =>
                    $request
                        ->assignedLeader
                        ->display_name,
            ];
        }

        if (
            $request->churchUnit
            && filled(
                $request
                    ->churchUnit
                    ->email
            )
        ) {
            $recipients[] = [
                'email' =>
                    $request
                        ->churchUnit
                        ->email,

                'name' =>
                    $request
                        ->churchUnit
                        ->name,
            ];
        }

        return $this->uniqueRecipients(
            $recipients
        );
    }

    private function uniqueRecipients(
        array $recipients
    ): array {
        return collect($recipients)
            ->filter(
                fn (
                    array $recipient
                ): bool =>
                    filled(
                        $recipient['email']
                        ?? null
                    )
            )
            ->map(
                function (
                    array $recipient
                ): array {
                    return [
                        'email' =>
                            Str::lower(
                                trim(
                                    $recipient['email']
                                )
                            ),

                        'name' =>
                            filled(
                                $recipient['name']
                                ?? null
                            )
                                ? trim(
                                    $recipient['name']
                                )
                                : 'Church Unit Team',
                    ];
                }
            )
            ->unique('email')
            ->values()
            ->all();
    }

    private function sendSafely(
        string $email,
        string $subject,
        string $view,
        array $data,
        string $event,
        UnitMembershipRequest $request
    ): void {
        try {
            $html = View::make(
                $view,
                $data
            )->render();

            $this->mailer->send(
                Str::lower(
                    trim($email)
                ),
                $subject,
                $html
            );

            Log::info(
                'Unit membership email sent.',
                [
                    'event' =>
                        $event,

                    'request_id' =>
                        $request->id,

                    'reference' =>
                        $request
                            ->submission_reference,

                    'recipient' =>
                        Str::lower(
                            trim($email)
                        ),
                ]
            );
        } catch (Throwable $exception) {
            report($exception);

            Log::error(
                'Unit membership email failed.',
                [
                    'event' =>
                        $event,

                    'request_id' =>
                        $request->id,

                    'reference' =>
                        $request
                            ->submission_reference,

                    'recipient' =>
                        Str::lower(
                            trim($email)
                        ),

                    'error' =>
                        $exception->getMessage(),
                ]
            );
        }
    }

    private function adminUrl(
        UnitMembershipRequest $request
    ): string {
        try {
            return UnitMembershipRequestResource::getUrl(
                'view',
                [
                    'record' =>
                        $request,
                ]
            );
        } catch (Throwable) {
            return url('/hub');
        }
    }
}