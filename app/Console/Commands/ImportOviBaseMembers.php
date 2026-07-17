<?php

namespace App\Console\Commands;

use App\Models\ChurchUnit;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ImportOviBaseMembers extends Command
{
    protected $signature = 'ovibase:import-members
        {--dry-run : Analyse the import without writing records}
        {--update-existing : Fill blank fields on previously matched members}';

    protected $description =
        'Import Fresh Fountain members from the legacy OviBase database.';

    private const TENANT_ID =
        'cmjne5xfj0000ak3n11khqh89';

    private const LEGACY_SOURCE =
        'ovibase';

    private bool $dryRun = false;

    private bool $updateExisting = false;

    private array $stats = [
        'legacy_members_found' => 0,
        'members_created' => 0,
        'matched_by_legacy_id' => 0,
        'matched_by_email' => 0,
        'matched_by_mobile' => 0,
        'existing_members_updated' => 0,
        'units_mapped' => 0,
        'units_not_mapped' => 0,
        'invalid_dates_removed' => 0,
        'records_skipped' => 0,
    ];

    private array $unmappedUnits = [];

    public function handle(): int
    {
        $this->dryRun =
            (bool) $this->option('dry-run');

        $this->updateExisting =
            (bool) $this->option(
                'update-existing'
            );

        $this->newLine();

        $this->info(
            $this->dryRun
                ? 'OviBase members import — DRY RUN'
                : 'Importing OviBase members'
        );

        if (! $this->validateEnvironment()) {
            return self::FAILURE;
        }

        try {
            $legacyMembers = DB::connection(
                'ovibase'
            )
                ->table('Member')
                ->where(
                    'tenantId',
                    self::TENANT_ID
                )
                ->orderBy('createdAt')
                ->orderBy('id')
                ->get();

            $this->stats[
                'legacy_members_found'
            ] = $legacyMembers->count();

            $this->info(
                "{$legacyMembers->count()} legacy members found."
            );

            $unitLookup =
                $this->buildUnitLookup();

            foreach (
                $legacyMembers as $legacyMember
            ) {
                $this->importMember(
                    $legacyMember,
                    $unitLookup
                );
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

    private function validateEnvironment(): bool
    {
        try {
            $schema = DB::connection(
                'ovibase'
            )->getSchemaBuilder();

            if (! $schema->hasTable('Member')) {
                $this->error(
                    'The OviBase Member table was not found.'
                );

                return false;
            }
        } catch (Throwable $exception) {
            $this->error(
                'Could not connect to the OviBase database: '
                . $exception->getMessage()
            );

            return false;
        }

        if (
            ! DB::getSchemaBuilder()
                ->hasTable('members')
        ) {
            $this->error(
                'The destination members table does not exist.'
            );

            return false;
        }

        if (
            ! DB::getSchemaBuilder()
                ->hasTable('church_units')
        ) {
            $this->error(
                'The destination church_units table does not exist.'
            );

            return false;
        }

        return true;
    }

    private function importMember(
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
            $this->warn(
                "Skipped legacy member {$legacy->id}: no first name."
            );

            $this->stats['records_skipped']++;

            return;
        }

        $email = $this->cleanEmail(
            $legacy->email ?? null
        );

        $mobile = $this->cleanPhone(
            $legacy->mobileNumber ?? null
        );

        $unitName = $this->cleanText(
            $legacy->churchUnit ?? null
        );

        $leaderName = $this->cleanText(
            $legacy->churchLeader ?? null
        );

        $churchUnitId = $this->resolveUnitId(
            $unitName,
            $unitLookup
        );

        $dateOfBirth = $this->safeDate(
            $legacy->dateOfBirth ?? null
        );

        $anniversaryDate = $this->safeDate(
            $legacy->anniversaryDate ?? null
        );

        $customFields =
            $this->decodeLegacyJson(
                $legacy->customFields ?? null
            );

        $payload = [
            'tenant_id' =>
                $legacy->tenantId ?? null,

            'first_name' =>
                $legacy->firstName ?? null,

            'last_name' =>
                $legacy->lastName ?? null,

            'gender' =>
                $legacy->gender ?? null,

            'mobile_number' =>
                $legacy->mobileNumber ?? null,

            'email' =>
                $legacy->email ?? null,

            'date_of_birth' =>
                $legacy->dateOfBirth ?? null,

            'anniversary_date' =>
                $legacy->anniversaryDate
                    ?? null,

            'church_unit' =>
                $legacy->churchUnit ?? null,

            'church_leader' =>
                $legacy->churchLeader ?? null,

            'custom_fields' =>
                $customFields,

            'created_at' =>
                $legacy->createdAt ?? null,

            'updated_at' =>
                $legacy->updatedAt ?? null,
        ];

        [
            $member,
            $matchType,
        ] = $this->findExistingMember(
            (string) $legacy->id,
            $email,
            $mobile
        );

        if ($member) {
            $this->handleExistingMember(
                $member,
                $matchType,
                [
                    'church_unit_id' =>
                        $churchUnitId,

                    'first_name' =>
                        $firstName,

                    'last_name' =>
                        $lastName,

                    'gender' =>
                        $this->normaliseGender(
                            $legacy->gender
                                ?? null
                        ),

                    'date_of_birth' =>
                        $dateOfBirth,

                    'anniversary_date' =>
                        $anniversaryDate,

                    'email' => $email,

                    'mobile_number' =>
                        $mobile,

                    'legacy_church_leader_name' =>
                        $leaderName,

                    'legacy_source' =>
                        self::LEGACY_SOURCE,

                    'legacy_id' =>
                        (string) $legacy->id,

                    'legacy_payload' =>
                        $payload,
                ]
            );

            return;
        }

        $this->stats['members_created']++;

        if ($this->dryRun) {
            return;
        }

        $createdAt = $this->safeTimestamp(
            $legacy->createdAt ?? null
        ) ?? now();

        $updatedAt = $this->safeTimestamp(
            $legacy->updatedAt ?? null
        ) ?? $createdAt;

        /*
         * We use DB::table() here so that the original
         * legacy timestamps are retained exactly.
         */
        DB::table('members')->insert([
            'user_id' => null,

            'church_unit_id' =>
                $churchUnitId,

            'title' => null,

            'first_name' =>
                $firstName,

            'middle_name' => null,

            'last_name' =>
                $lastName,

            'gender' =>
                $this->normaliseGender(
                    $legacy->gender ?? null
                ),

            'date_of_birth' =>
                $dateOfBirth,

            'anniversary_date' =>
                $anniversaryDate,

            'email' => $email,

            'mobile_number' =>
                $mobile,

            'alternative_phone' => null,

            'address' => null,

            'postcode' => null,

            'membership_status' =>
                Member::STATUS_ACTIVE,

            /*
             * OviBase does not contain a reliable
             * church-joining date, so we leave this blank
             * rather than treating record creation as
             * the date they joined the church.
             */
            'joined_at' => null,

            'legacy_church_leader_name' =>
                $leaderName,

            'notes' => null,

            'is_active' => true,

            /*
             * OviBase has no explicit consent fields.
             * These are deliberately disabled until the
             * church reviews its communication basis.
             */
            'email_consent' => false,

            'sms_consent' => false,

            'do_not_contact' => false,

            'legacy_source' =>
                self::LEGACY_SOURCE,

            'legacy_id' =>
                (string) $legacy->id,

            'legacy_payload' =>
                json_encode(
                    $payload,
                    JSON_THROW_ON_ERROR
                ),

            'created_at' =>
                $createdAt,

            'updated_at' =>
                $updatedAt,
        ]);
    }

    private function findExistingMember(
        string $legacyId,
        ?string $email,
        ?string $mobile
    ): array {
        $member = Member::query()
            ->where(
                'legacy_source',
                self::LEGACY_SOURCE
            )
            ->where(
                'legacy_id',
                $legacyId
            )
            ->first();

        if ($member) {
            return [
                $member,
                'legacy_id',
            ];
        }

        if (filled($email)) {
            $member = Member::query()
                ->whereRaw(
                    'LOWER(email) = ?',
                    [Str::lower($email)]
                )
                ->first();

            if ($member) {
                return [
                    $member,
                    'email',
                ];
            }
        }

        if (filled($mobile)) {
            $normalisedMobile =
                $this->phoneComparisonValue(
                    $mobile
                );

            $member = Member::query()
                ->whereNotNull(
                    'mobile_number'
                )
                ->get()
                ->first(
                    fn (Member $candidate): bool =>
                        $this->phoneComparisonValue(
                            $candidate
                                ->mobile_number
                        )
                        === $normalisedMobile
                );

            if ($member) {
                return [
                    $member,
                    'mobile',
                ];
            }
        }

        return [
            null,
            null,
        ];
    }

    private function handleExistingMember(
        Member $member,
        string $matchType,
        array $incoming
    ): void {
        match ($matchType) {
            'legacy_id' =>
                $this->stats[
                    'matched_by_legacy_id'
                ]++,

            'email' =>
                $this->stats[
                    'matched_by_email'
                ]++,

            'mobile' =>
                $this->stats[
                    'matched_by_mobile'
                ]++,

            default => null,
        };

        if (! $this->updateExisting) {
            return;
        }

        $updates = [];

        /*
         * Migration identifiers may safely be attached
         * to a record matched through email or mobile.
         */
        if (
            blank($member->legacy_source)
            && blank($member->legacy_id)
        ) {
            $updates['legacy_source'] =
                self::LEGACY_SOURCE;

            $updates['legacy_id'] =
                $incoming['legacy_id'];
        }

        $updates['legacy_payload'] =
            $incoming['legacy_payload'];

        foreach (
            [
                'church_unit_id',
                'last_name',
                'gender',
                'date_of_birth',
                'anniversary_date',
                'email',
                'mobile_number',
                'legacy_church_leader_name',
            ] as $field
        ) {
            if (
                blank($member->{$field})
                && filled($incoming[$field])
            ) {
                $updates[$field] =
                    $incoming[$field];
            }
        }

        if ($updates === []) {
            return;
        }

        $this->stats[
            'existing_members_updated'
        ]++;

        if ($this->dryRun) {
            return;
        }

        $member->update($updates);
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
                        $this->normaliseLookup(
                            $unit->name
                        )
                    ] = $unit->id;

                    if (filled($unit->alias)) {
                        $lookup[
                            $this->normaliseLookup(
                                $unit->alias
                            )
                        ] = $unit->id;
                    }
                }
            );

        return $lookup;
    }

    private function resolveUnitId(
        ?string $legacyUnit,
        array $lookup
    ): ?int {
        if (
            blank($legacyUnit)
            || in_array(
                trim($legacyUnit),
                ['-', '--'],
                true
            )
        ) {
            return null;
        }

        $key = $this->normaliseLookup(
            $legacyUnit
        );

        $unitId = $lookup[$key] ?? null;

        if ($unitId) {
            $this->stats['units_mapped']++;

            return (int) $unitId;
        }

        $this->stats['units_not_mapped']++;

        $this->unmappedUnits[$legacyUnit] =
            ($this->unmappedUnits[
                $legacyUnit
            ] ?? 0) + 1;

        return null;
    }

    private function safeDate(
        mixed $value
    ): ?string {
        if (blank($value)) {
            return null;
        }

        try {
            $date = Carbon::parse(
                (string) $value
            );

            /*
             * MySQL DATE does not support years below
             * 1000. The legacy database contains values
             * such as 0001-01-01, which are placeholders
             * rather than genuine dates.
             */
            if ($date->year < 1000) {
                $this->stats[
                    'invalid_dates_removed'
                ]++;

                return null;
            }

            return $date->toDateString();
        } catch (Throwable) {
            $this->stats[
                'invalid_dates_removed'
            ]++;

            return null;
        }
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

    private function normaliseGender(
        mixed $value
    ): ?string {
        $gender = Str::lower(
            trim((string) $value)
        );

        return match ($gender) {
            'male', 'm' => 'male',
            'female', 'f' => 'female',
            'prefer not to say',
            'prefer_not_to_say' =>
                'prefer_not_to_say',

            default => null,
        };
    }

    private function cleanText(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $clean = trim((string) $value);

        if (
            $clean === ''
            || in_array(
                $clean,
                ['-', '--', 'null'],
                true
            )
        ) {
            return null;
        }

        return $clean;
    }

    private function cleanEmail(
        mixed $value
    ): ?string {
        $email = $this->cleanText(
            $value
        );

        if (
            blank($email)
            || ! filter_var(
                $email,
                FILTER_VALIDATE_EMAIL
            )
        ) {
            return null;
        }

        return Str::lower($email);
    }

    private function cleanPhone(
        mixed $value
    ): ?string {
        $phone = $this->cleanText(
            $value
        );

        if (blank($phone)) {
            return null;
        }

        return preg_replace(
            '/\s+/',
            ' ',
            trim($phone)
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

    private function normaliseLookup(
        string $value
    ): string {
        return Str::of($value)
            ->lower()
            ->replace(['&'], 'and')
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

    private function decodeLegacyJson(
        mixed $value
    ): mixed {
        if (
            $value === null
            || $value === ''
            || $value === '"null"'
        ) {
            return null;
        }

        if (
            is_array($value)
            || is_object($value)
        ) {
            return $value;
        }

        try {
            $decoded = json_decode(
                (string) $value,
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            if ($decoded === 'null') {
                return null;
            }

            return $decoded;
        } catch (Throwable) {
            return [
                '_raw' => (string) $value,
            ];
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

        if ($this->unmappedUnits !== []) {
            $this->newLine();

            $this->warn(
                'Unmapped legacy church-unit values:'
            );

            $this->table(
                ['Legacy unit', 'Members'],
                collect($this->unmappedUnits)
                    ->sortDesc()
                    ->map(
                        fn (
                            int $count,
                            string $unit
                        ): array => [
                            $unit,
                            $count,
                        ]
                    )
                    ->values()
                    ->all()
            );
        }

        $this->newLine();

        if ($this->dryRun) {
            $this->warn(
                'Dry run complete. No records were written.'
            );

            $this->line(
                'Run without --dry-run when the report looks correct.'
            );
        } else {
            $this->info(
                'OviBase member import completed.'
            );

            $this->line(
                'The command may be safely rerun; legacy IDs prevent duplicates.'
            );
        }
    }
}