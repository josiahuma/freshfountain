<?php

namespace App\Console\Commands;

use App\Models\ChurchUnit;
use App\Models\Leader;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ImportOviBaseLeaders extends Command
{
    protected $signature = 'ovibase:import-leaders
        {--dry-run : Analyse without writing records}
        {--assign-members : Assign imported members to their matching leaders}';

    protected $description =
        'Import Fresh Fountain leaders from OviBase and optionally assign members.';

    private const TENANT_ID =
        'cmjne5xfj0000ak3n11khqh89';

    private const LEGACY_SOURCE =
        'ovibase';

    private bool $dryRun = false;

    private array $stats = [
        'legacy_leaders_found' => 0,
        'leaders_created' => 0,
        'leaders_matched' => 0,
        'leaders_linked_to_members' => 0,
        'units_mapped' => 0,
        'units_not_mapped' => 0,
        'member_assignments_created' => 0,
        'member_assignments_not_matched' => 0,
    ];

    public function handle(): int
    {
        $this->dryRun =
            (bool) $this->option('dry-run');

        $this->info(
            $this->dryRun
                ? 'OviBase leaders import — DRY RUN'
                : 'Importing OviBase leaders'
        );

        try {
            $legacyLeaders = DB::connection('ovibase')
                ->table('Leader')
                ->where(
                    'tenantId',
                    self::TENANT_ID
                )
                ->orderBy('createdAt')
                ->get();

            $this->stats[
                'legacy_leaders_found'
            ] = $legacyLeaders->count();

            $unitLookup = $this->buildUnitLookup();

            foreach ($legacyLeaders as $legacy) {
                $this->importLeader(
                    $legacy,
                    $unitLookup
                );
            }

            if ($this->option('assign-members')) {
                $this->assignMembersToLeaders();
            }

            $this->displaySummary();

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    private function importLeader(
        object $legacy,
        array $unitLookup
    ): void {
        $firstName = $this->cleanText(
            $legacy->firstName ?? null
        );

        $lastName = $this->cleanText(
            $legacy->lastName ?? null
        );

        if (blank($firstName)) {
            return;
        }

        $email = $this->cleanEmail(
            $legacy->email ?? null
        );

        $mobile = $this->cleanPhone(
            $legacy->mobileNumber ?? null
        );

        $churchUnitId = $this->resolveUnitId(
            $legacy->churchUnit ?? null,
            $unitLookup
        );

        $existing = Leader::query()
            ->where(
                'legacy_source',
                self::LEGACY_SOURCE
            )
            ->where(
                'legacy_id',
                $legacy->id
            )
            ->first();

        if (! $existing && filled($email)) {
            $existing = Leader::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [Str::lower($email)]
                )
                ->first();
        }

        if ($existing) {
            $this->stats['leaders_matched']++;

            return;
        }

        $linkedMember = $this->findMatchingMember(
            $email,
            $mobile,
            $firstName,
            $lastName
        );

        if ($linkedMember) {
            $this->stats[
                'leaders_linked_to_members'
            ]++;
        }

        $this->stats['leaders_created']++;

        if ($this->dryRun) {
            return;
        }

        $createdAt = $this->safeTimestamp(
            $legacy->createdAt ?? null
        ) ?? now();

        $updatedAt = $this->safeTimestamp(
            $legacy->updatedAt ?? null
        ) ?? $createdAt;

        DB::table('leaders')->insert([
            'member_id' =>
                $linkedMember?->id,

            'church_unit_id' =>
                $churchUnitId,

            'first_name' =>
                $firstName,

            'middle_name' => null,

            'last_name' =>
                $lastName,

            'email' => $email,

            'mobile_number' =>
                $mobile,

            'leadership_role' =>
                'Unit Leader',

            'started_at' => null,

            'ended_at' => null,

            'is_active' => true,

            'notes' => null,

            'legacy_source' =>
                self::LEGACY_SOURCE,

            'legacy_id' =>
                (string) $legacy->id,

            'legacy_payload' =>
                json_encode([
                    'tenant_id' =>
                        $legacy->tenantId ?? null,

                    'church_unit' =>
                        $legacy->churchUnit ?? null,

                    'members_count' =>
                        $legacy->membersCount ?? null,

                    'created_at' =>
                        $legacy->createdAt ?? null,

                    'updated_at' =>
                        $legacy->updatedAt ?? null,
                ], JSON_THROW_ON_ERROR),

            'created_at' =>
                $createdAt,

            'updated_at' =>
                $updatedAt,
        ]);
    }

    private function assignMembersToLeaders(): void
    {
        $members = Member::query()
            ->where('legacy_source', 'ovibase')
            ->whereNotNull(
                'legacy_church_leader_name'
            )
            ->whereNull('leader_id')
            ->get();

        foreach ($members as $member) {
            $leaderName = $this->normaliseName(
                $member->legacy_church_leader_name
            );

            if (
                blank($leaderName)
                || $leaderName === '--'
            ) {
                continue;
            }

            $leader = Leader::query()
                ->get()
                ->first(
                    fn (Leader $candidate): bool =>
                        $this->normaliseName(
                            $candidate->full_name
                        ) === $leaderName
                );

            if (! $leader) {
                $this->stats[
                    'member_assignments_not_matched'
                ]++;

                $this->warn(
                    "No leader matched: {$member->legacy_church_leader_name}"
                );

                continue;
            }

            $this->stats[
                'member_assignments_created'
            ]++;

            if ($this->dryRun) {
                continue;
            }

            $member->update([
                'leader_id' =>
                    $leader->id,
            ]);
        }
    }

    private function findMatchingMember(
        ?string $email,
        ?string $mobile,
        string $firstName,
        ?string $lastName
    ): ?Member {
        if (filled($email)) {
            $member = Member::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [Str::lower($email)]
                )
                ->first();

            if ($member) {
                return $member;
            }
        }

        if (filled($mobile)) {
            $normalisedMobile =
                $this->normalisePhone($mobile);

            $member = Member::query()
                ->whereNotNull('mobile_number')
                ->get()
                ->first(
                    fn (Member $candidate): bool =>
                        $this->normalisePhone(
                            $candidate->mobile_number
                        ) === $normalisedMobile
                );

            if ($member) {
                return $member;
            }
        }

        $expectedName = $this->normaliseName(
            collect([
                $firstName,
                $lastName,
            ])
                ->filter()
                ->implode(' ')
        );

        return Member::query()
            ->get()
            ->first(
                fn (Member $candidate): bool =>
                    $this->normaliseName(
                        $candidate->display_name
                    ) === $expectedName
            );
    }

    private function buildUnitLookup(): array
    {
        $lookup = [];

        ChurchUnit::query()
            ->get()
            ->each(
                function (
                    ChurchUnit $unit
                ) use (&$lookup): void {
                    $lookup[
                        $this->normaliseName(
                            $unit->name
                        )
                    ] = $unit->id;

                    if (filled($unit->alias)) {
                        $lookup[
                            $this->normaliseName(
                                $unit->alias
                            )
                        ] = $unit->id;
                    }
                }
            );

        return $lookup;
    }

    private function resolveUnitId(
        mixed $value,
        array $lookup
    ): ?int {
        $unit = $this->cleanText($value);

        if (blank($unit)) {
            return null;
        }

        $unitId = $lookup[
            $this->normaliseName($unit)
        ] ?? null;

        if ($unitId) {
            $this->stats['units_mapped']++;

            return (int) $unitId;
        }

        $this->stats['units_not_mapped']++;

        return null;
    }

    private function cleanText(
        mixed $value
    ): ?string {
        $value = trim((string) $value);

        if (
            $value === ''
            || in_array(
                strtolower($value),
                ['-', '--', 'null'],
                true
            )
        ) {
            return null;
        }

        return $value;
    }

    private function cleanEmail(
        mixed $value
    ): ?string {
        $email = $this->cleanText($value);

        return filled($email)
            && filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
                ? Str::lower($email)
                : null;
    }

    private function cleanPhone(
        mixed $value
    ): ?string {
        return $this->cleanText($value);
    }

    private function normalisePhone(
        mixed $value
    ): string {
        return preg_replace(
            '/\D+/',
            '',
            (string) $value
        ) ?? '';
    }

    private function normaliseName(
        mixed $value
    ): string {
        return Str::of((string) $value)
            ->lower()
            ->replaceMatches(
                '/[^a-z0-9]+/',
                ' '
            )
            ->replaceMatches(
                '/\s+/',
                ' '
            )
            ->trim()
            ->toString();
    }

    private function safeTimestamp(
        mixed $value
    ): ?string {
        if (blank($value)) {
            return null;
        }

        try {
            return Carbon::parse(
                (string) $value
            )->format('Y-m-d H:i:s');
        } catch (Throwable) {
            return null;
        }
    }

    private function displaySummary(): void
    {
        $this->newLine();

        $this->table(
            ['Result', 'Count'],
            collect($this->stats)
                ->map(
                    fn (
                        int $count,
                        string $label
                    ): array => [
                        Str::headline($label),
                        number_format($count),
                    ]
                )
                ->values()
                ->all()
        );

        if ($this->dryRun) {
            $this->warn(
                'Dry run complete. Nothing was written.'
            );
        } else {
            $this->info(
                'OviBase leader import completed.'
            );
        }
    }
}