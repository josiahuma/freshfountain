<?php

namespace App\Services;

use App\Models\ChurchUnit;
use App\Models\Leader;
use App\Models\Member;
use App\Models\UnitMembershipRequest;
use App\Services\Notifications\UnitMembershipNotificationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UnitRequestService
{
    public function __construct(
        private readonly UnitMembershipNotificationService $notifications
    ) {
    }

    public function submit(
        ChurchUnit $churchUnit,
        array $data
    ): UnitMembershipRequest {
        $membershipRequest =
            DB::transaction(
                function () use (
                    $churchUnit,
                    $data
                ): UnitMembershipRequest {
                    $email =
                        $this->normaliseEmail(
                            $data['email']
                            ?? null
                        );

                    $mobile =
                        $this->normalisePhone(
                            $data['mobile_number']
                            ?? null
                        );

                    $phoneComparison =
                        $this->phoneComparisonValue(
                            $mobile
                        );

                    $member =
                        $this->findMember(
                            $email,
                            $phoneComparison
                        );

                    if (! $member) {
                        $member =
                            $this->createVisitor(
                                $data,
                                $email,
                                $mobile
                            );
                    }

                    $this->ensureNotAlreadyMember(
                        $member,
                        $churchUnit
                    );

                    $this->ensureNoOpenRequest(
                        $member,
                        $churchUnit,
                        $email,
                        $phoneComparison
                    );

                    $assignedLeader =
                        $this->randomActiveLeader(
                            $churchUnit
                        );

                    return UnitMembershipRequest::query()
                        ->create([
                            'member_id' =>
                                $member->id,

                            'church_unit_id' =>
                                $churchUnit->id,

                            'assigned_leader_id' =>
                                $assignedLeader?->id,

                            'first_name' =>
                                $this->cleanName(
                                    $data['first_name']
                                    ?? null
                                ),

                            'last_name' =>
                                $this->cleanName(
                                    $data['last_name']
                                    ?? null
                                ),

                            'email' =>
                                $email,

                            'mobile_number' =>
                                $mobile,

                            'message' =>
                                filled(
                                    $data['message']
                                    ?? null
                                )
                                    ? trim(
                                        (string)
                                        $data['message']
                                    )
                                    : null,

                            'status' =>
                                $assignedLeader
                                    ? UnitMembershipRequest::STATUS_ASSIGNED
                                    : UnitMembershipRequest::STATUS_PENDING,

                            'assigned_at' =>
                                $assignedLeader
                                    ? now()
                                    : null,

                            'submitted_at' =>
                                now(),

                            'source' =>
                                'website',
                        ]);
                },
                3
            );

        $membershipRequest->load([
            'churchUnit',
            'assignedLeader',
            'member',
        ]);

        $this->notifications
            ->requestSubmitted(
                $membershipRequest
            );

        return $membershipRequest;
    }

    private function findMember(
        ?string $email,
        string $phoneComparison
    ): ?Member {
        if (filled($email)) {
            $member = Member::query()
                ->whereNotNull('email')
                ->whereRaw(
                    'LOWER(TRIM(email)) = ?',
                    [$email]
                )
                ->first();

            if ($member) {
                return $member;
            }
        }

        if ($phoneComparison === '') {
            return null;
        }

        return Member::query()
            ->whereNotNull(
                'mobile_number'
            )
            ->get()
            ->first(
                fn (
                    Member $candidate
                ): bool =>
                    $this->phoneComparisonValue(
                        $candidate
                            ->mobile_number
                    ) === $phoneComparison
            );
    }

    private function createVisitor(
        array $data,
        ?string $email,
        ?string $mobile
    ): Member {
        $existing = $this->findMember(
            $email,
            $this->phoneComparisonValue(
                $mobile
            )
        );

        if ($existing) {
            return $existing;
        }

        return Member::query()->create([
            'first_name' =>
                $this->cleanName(
                    $data['first_name']
                    ?? null
                ),

            'last_name' =>
                $this->cleanName(
                    $data['last_name']
                    ?? null
                ),

            'email' =>
                $email,

            'mobile_number' =>
                $mobile,

            'membership_status' =>
                Member::STATUS_VISITOR,

            'is_active' =>
                true,

            'email_consent' =>
                false,

            'sms_consent' =>
                false,

            'do_not_contact' =>
                false,

            'church_unit_id' =>
                null,

            'leader_id' =>
                null,

            'notes' =>
                'Created automatically from a public unit membership request.',
        ]);
    }

    private function ensureNotAlreadyMember(
        Member $member,
        ChurchUnit $churchUnit
    ): void {
        $alreadyMember = DB::table(
            'church_unit_member'
        )
            ->where(
                'member_id',
                $member->id
            )
            ->where(
                'church_unit_id',
                $churchUnit->id
            )
            ->where(
                'status',
                'active'
            )
            ->exists();

        if (! $alreadyMember) {
            return;
        }

        throw ValidationException::withMessages([
            'email' =>
                "Our records show that you already belong to {$churchUnit->name}.",
        ]);
    }

    private function ensureNoOpenRequest(
        Member $member,
        ChurchUnit $churchUnit,
        ?string $email,
        string $phoneComparison
    ): void {
        $openStatuses = [
            UnitMembershipRequest::STATUS_PENDING,
            UnitMembershipRequest::STATUS_ASSIGNED,
            UnitMembershipRequest::STATUS_CONTACTED,
            UnitMembershipRequest::STATUS_APPROVED,
        ];

        $requests =
            UnitMembershipRequest::query()
                ->where(
                    'church_unit_id',
                    $churchUnit->id
                )
                ->whereIn(
                    'status',
                    $openStatuses
                )
                ->where(
                    function (
                        Builder $query
                    ) use (
                        $member,
                        $email
                    ): void {
                        $query->where(
                            'member_id',
                            $member->id
                        );

                        if (filled($email)) {
                            $query->orWhereRaw(
                                'LOWER(TRIM(email)) = ?',
                                [$email]
                            );
                        }
                    }
                )
                ->get();

        $duplicate =
            $requests->contains(
                function (
                    UnitMembershipRequest $request
                ) use (
                    $member,
                    $email,
                    $phoneComparison
                ): bool {
                    if (
                        (int)
                        $request->member_id
                        ===
                        (int)
                        $member->id
                    ) {
                        return true;
                    }

                    if (
                        filled($email)
                        && filled(
                            $request->email
                        )
                        && $this->normaliseEmail(
                            $request->email
                        ) === $email
                    ) {
                        return true;
                    }

                    if (
                        $phoneComparison
                        !== ''
                        && $this
                            ->phoneComparisonValue(
                                $request
                                    ->mobile_number
                            )
                        === $phoneComparison
                    ) {
                        return true;
                    }

                    return false;
                }
            );

        if (! $duplicate) {
            return;
        }

        throw ValidationException::withMessages([
            'email' =>
                "You already have an active request to join {$churchUnit->name}.",
        ]);
    }

    private function randomActiveLeader(
        ChurchUnit $churchUnit
    ): ?Leader {
        return $churchUnit
            ->leaders()
            ->where(
                'is_active',
                true
            )
            ->inRandomOrder()
            ->first();
    }

    private function normaliseEmail(
        mixed $value
    ): ?string {
        if (blank($value)) {
            return null;
        }

        return Str::lower(
            trim(
                (string) $value
            )
        );
    }

    private function normalisePhone(
        mixed $value
    ): ?string {
        if (blank($value)) {
            return null;
        }

        $phone = trim(
            (string) $value
        );

        $phone = preg_replace(
            '/\s+/',
            ' ',
            $phone
        );

        return filled($phone)
            ? $phone
            : null;
    }

    private function phoneComparisonValue(
        mixed $value
    ): string {
        $digits = preg_replace(
            '/\D+/',
            '',
            (string) $value
        ) ?? '';

        if (strlen($digits) >= 10) {
            return substr(
                $digits,
                -10
            );
        }

        return $digits;
    }

    private function cleanName(
        mixed $value
    ): ?string {
        if (blank($value)) {
            return null;
        }

        $name = preg_replace(
            '/\s+/',
            ' ',
            trim(
                (string) $value
            )
        );

        return filled($name)
            ? $name
            : null;
    }
}