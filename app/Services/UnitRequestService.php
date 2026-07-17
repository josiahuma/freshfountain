<?php

namespace App\Services;

use App\Models\ChurchUnit;
use App\Models\Leader;
use App\Models\Member;
use App\Models\UnitMembershipRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UnitRequestService
{
    public function submit(
        ChurchUnit $churchUnit,
        array $data
    ): UnitMembershipRequest {
        return DB::transaction(
            function () use (
                $churchUnit,
                $data
            ): UnitMembershipRequest {
                $email = $this->normaliseEmail(
                    $data['email'] ?? null
                );

                $mobile = $this->cleanPhone(
                    $data['mobile_number'] ?? null
                );

                $member = $this->findMember(
                    $email,
                    $mobile
                );

                if (! $member) {
                    $member = $this->createVisitor(
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
                    $mobile
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
                            trim(
                                (string)
                                $data['first_name']
                            ),

                        'last_name' =>
                            filled(
                                $data['last_name']
                                ?? null
                            )
                                ? trim(
                                    (string)
                                    $data['last_name']
                                )
                                : null,

                        'email' => $email,

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
            }
        );
    }

    private function findMember(
        ?string $email,
        ?string $mobile
    ): ?Member {
        if (filled($email)) {
            $member = Member::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [$email]
                )
                ->first();

            if ($member) {
                return $member;
            }
        }

        if (filled($mobile)) {
            $comparison =
                $this->phoneComparisonValue(
                    $mobile
                );

            if ($comparison !== '') {
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
                            ) === $comparison
                    );
            }
        }

        return null;
    }

    private function createVisitor(
        array $data,
        ?string $email,
        ?string $mobile
    ): Member {
        return Member::query()->create([
            'first_name' =>
                trim(
                    (string)
                    $data['first_name']
                ),

            'last_name' =>
                filled(
                    $data['last_name']
                    ?? null
                )
                    ? trim(
                        (string)
                        $data['last_name']
                    )
                    : null,

            'email' => $email,

            'mobile_number' =>
                $mobile,

            'membership_status' =>
                Member::STATUS_VISITOR,

            'is_active' => true,

            /*
             * Consent must not be assumed merely
             * because somebody submitted this form.
             */
            'email_consent' => false,
            'sms_consent' => false,
            'do_not_contact' => false,

            /*
             * The requested unit is not assigned here.
             * Membership is created only after approval.
             */
            'church_unit_id' => null,
            'leader_id' => null,

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
        ?string $mobile
    ): void {
        $openStatuses = [
            UnitMembershipRequest::STATUS_PENDING,
            UnitMembershipRequest::STATUS_ASSIGNED,
            UnitMembershipRequest::STATUS_CONTACTED,
            UnitMembershipRequest::STATUS_APPROVED,
        ];

        $query = UnitMembershipRequest::query()
            ->where(
                'church_unit_id',
                $churchUnit->id
            )
            ->whereIn(
                'status',
                $openStatuses
            )
            ->where(
                function ($requestQuery) use (
                    $member,
                    $email,
                    $mobile
                ): void {
                    $requestQuery->where(
                        'member_id',
                        $member->id
                    );

                    if (filled($email)) {
                        $requestQuery->orWhereRaw(
                            'LOWER(email) = ?',
                            [$email]
                        );
                    }

                    if (filled($mobile)) {
                        $requestQuery->orWhere(
                            'mobile_number',
                            $mobile
                        );
                    }
                }
            );

        if (! $query->exists()) {
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
            trim((string) $value)
        );
    }

    private function cleanPhone(
        mixed $value
    ): ?string {
        if (blank($value)) {
            return null;
        }

        return preg_replace(
            '/\s+/',
            ' ',
            trim((string) $value)
        );
    }

    private function phoneComparisonValue(
        mixed $value
    ): string {
        return preg_replace(
            '/\D+/',
            '',
            (string) $value
        ) ?? '';
    }
}