<?php

namespace App\Console\Commands;

use App\Models\Donation;
use App\Models\DonationFund;
use App\Models\ExpenseCategory;
use App\Models\FinanceTransaction;
use App\Models\IncomeCategory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ImportOviBaseFinance extends Command
{
    protected $signature = 'ovibase:import-finance
        {--dry-run : Analyse the import without writing records}
        {--update-existing : Update previously imported records}
        {--default-fund=Offering : Donation fund used for legacy donations without a fund reference}';

    protected $description =
        'Import finance categories, donation funds, donations and finance transactions from OviBase.';

    private const TENANT_ID =
        'cmjne5xfj0000ak3n11khqh89';

    private bool $dryRun = false;

    private bool $updateExisting = false;

    /** @var array<string, string> */
    private array $legacyTables = [];

    private array $stats = [
        'income_categories_found' => 0,
        'income_categories_created' => 0,
        'income_categories_updated' => 0,
        'expense_categories_found' => 0,
        'expense_categories_created' => 0,
        'expense_categories_updated' => 0,
        'donation_funds_found' => 0,
        'donation_funds_created' => 0,
        'donation_funds_updated' => 0,
        'donations_found' => 0,
        'donations_created' => 0,
        'donations_updated' => 0,
        'finance_records_found' => 0,
        'finance_transactions_created' => 0,
        'finance_transactions_updated' => 0,
        'finance_transactions_skipped' => 0,
        'records_failed' => 0,
    ];

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
                ? 'OviBase finance import — DRY RUN'
                : 'Importing OviBase finance data'
        );

        if (! $this->validateEnvironment()) {
            return self::FAILURE;
        }

        try {
            DB::transaction(function (): void {
                $this->importIncomeCategories();
                $this->importExpenseCategories();
                $this->importDonationFunds();
                $this->importDonations();
                $this->importFinanceTransactions();

                if ($this->dryRun) {
                    DB::rollBack();
                }
            });

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
            $this->legacyTables =
                $this->discoverLegacyTables();

            foreach (
                [
                    'incomecategory',
                    'expensecategory',
                    'donationfund',
                    'donation',
                    'finance',
                ] as $logicalName
            ) {
                if (! isset(
                    $this->legacyTables[$logicalName]
                )) {
                    $this->error(
                        'The OviBase '
                        . $logicalName
                        . ' table was not found.'
                    );

                    return false;
                }
            }
        } catch (Throwable $exception) {
            $this->error(
                'Could not connect to OviBase: '
                . $exception->getMessage()
            );

            return false;
        }

        foreach (
            [
                'income_categories',
                'expense_categories',
                'donation_funds',
                'donations',
                'finance_transactions',
            ] as $table
        ) {
            if (! DB::getSchemaBuilder()->hasTable($table)) {
                $this->error(
                    "The destination {$table} table does not exist."
                );

                return false;
            }
        }

        return true;
    }

    private function importIncomeCategories(): void
    {
        $records = DB::connection('ovibase')
            ->table($this->legacyTable('incomecategory'))
            ->where('tenantId', self::TENANT_ID)
            ->orderBy('createdAt')
            ->get();

        $this->stats['income_categories_found'] =
            $records->count();

        foreach ($records as $legacy) {
            $existing = IncomeCategory::query()
                ->where(
                    'legacy_ovibase_id',
                    (string) $legacy->id
                )
                ->first();

            $payload = [
                'name' => trim((string) $legacy->name),
                'description' => null,
                'is_active' => true,
                'sort_order' => 0,
                'legacy_ovibase_id' =>
                    (string) $legacy->id,
            ];

            if ($existing) {
                if ($this->updateExisting) {
                    $this->stats[
                        'income_categories_updated'
                    ]++;

                    if (! $this->dryRun) {
                        $existing->update($payload);
                    }
                }

                continue;
            }

            $matchedByName = IncomeCategory::query()
                ->whereRaw(
                    'LOWER(name) = ?',
                    [Str::lower($payload['name'])]
                )
                ->first();

            if ($matchedByName) {
                if ($this->updateExisting) {
                    $this->stats[
                        'income_categories_updated'
                    ]++;

                    if (! $this->dryRun) {
                        $matchedByName->update([
                            'legacy_ovibase_id' =>
                                (string) $legacy->id,
                        ]);
                    }
                }

                continue;
            }

            $this->stats[
                'income_categories_created'
            ]++;

            if (! $this->dryRun) {
                IncomeCategory::query()->create($payload);
            }
        }
    }

    private function importExpenseCategories(): void
    {
        $records = DB::connection('ovibase')
            ->table($this->legacyTable('expensecategory'))
            ->where('tenantId', self::TENANT_ID)
            ->orderBy('createdAt')
            ->get();

        $this->stats['expense_categories_found'] =
            $records->count();

        foreach ($records as $legacy) {
            $existing = ExpenseCategory::query()
                ->where(
                    'legacy_ovibase_id',
                    (string) $legacy->id
                )
                ->first();

            $payload = [
                'name' => trim((string) $legacy->name),
                'description' => null,
                'is_active' => true,
                'sort_order' => 0,
                'legacy_ovibase_id' =>
                    (string) $legacy->id,
            ];

            if ($existing) {
                if ($this->updateExisting) {
                    $this->stats[
                        'expense_categories_updated'
                    ]++;

                    if (! $this->dryRun) {
                        $existing->update($payload);
                    }
                }

                continue;
            }

            $matchedByName = ExpenseCategory::query()
                ->whereRaw(
                    'LOWER(name) = ?',
                    [Str::lower($payload['name'])]
                )
                ->first();

            if ($matchedByName) {
                if ($this->updateExisting) {
                    $this->stats[
                        'expense_categories_updated'
                    ]++;

                    if (! $this->dryRun) {
                        $matchedByName->update([
                            'legacy_ovibase_id' =>
                                (string) $legacy->id,
                        ]);
                    }
                }

                continue;
            }

            $this->stats[
                'expense_categories_created'
            ]++;

            if (! $this->dryRun) {
                ExpenseCategory::query()->create($payload);
            }
        }
    }

    private function importDonationFunds(): void
    {
        $records = DB::connection('ovibase')
            ->table($this->legacyTable('donationfund'))
            ->where('tenantId', self::TENANT_ID)
            ->orderBy('createdAt')
            ->get();

        $this->stats['donation_funds_found'] =
            $records->count();

        foreach ($records as $legacy) {
            $incomeCategory = IncomeCategory::query()
                ->whereRaw(
                    'LOWER(name) = ?',
                    [Str::lower(trim((string) $legacy->name))]
                )
                ->first();

            $existing = DonationFund::query()
                ->where(
                    'legacy_ovibase_id',
                    (string) $legacy->id
                )
                ->first();

            $payload = [
                'name' => trim((string) $legacy->name),
                'description' => null,
                'income_category_id' =>
                    $incomeCategory?->getKey(),
                'is_default' =>
                    (bool) $legacy->isDefault,
                'is_active' => true,
                'sort_order' => 0,
                'legacy_ovibase_id' =>
                    (string) $legacy->id,
            ];

            if ($existing) {
                if ($this->updateExisting) {
                    $this->stats[
                        'donation_funds_updated'
                    ]++;

                    if (! $this->dryRun) {
                        $existing->update($payload);
                    }
                }

                continue;
            }

            $matchedByName = DonationFund::query()
                ->whereRaw(
                    'LOWER(name) = ?',
                    [Str::lower($payload['name'])]
                )
                ->first();

            if ($matchedByName) {
                if ($this->updateExisting) {
                    $this->stats[
                        'donation_funds_updated'
                    ]++;

                    if (! $this->dryRun) {
                        $matchedByName->update([
                            'income_category_id' =>
                                $incomeCategory?->getKey(),
                            'legacy_ovibase_id' =>
                                (string) $legacy->id,
                        ]);
                    }
                }

                continue;
            }

            $this->stats[
                'donation_funds_created'
            ]++;

            if (! $this->dryRun) {
                DonationFund::query()->create($payload);
            }
        }
    }

    private function importDonations(): void
    {
        $records = DB::connection('ovibase')
            ->table($this->legacyTable('donation'))
            ->where('tenantId', self::TENANT_ID)
            ->orderBy('createdAt')
            ->get();

        $this->stats['donations_found'] =
            $records->count();

        $defaultFund = $this->resolveDefaultFund();

        foreach ($records as $legacy) {
            try {
                $existing = Donation::query()
                    ->where(
                        'legacy_ovibase_id',
                        (string) $legacy->id
                    )
                    ->first();

                $payload = [
                    'donation_fund_id' =>
                        $defaultFund?->getKey(),
                    'member_id' => null,
                    'amount' => $legacy->amount,
                    'currency' => strtoupper(
                        (string) $legacy->currency
                    ),
                    'payment_method' =>
                        'online_payment',
                    'is_recurring' =>
                        (bool) $legacy->isRecurring,
                    'recurring_interval' =>
                        $this->normaliseInterval(
                            $legacy->interval ?? null
                        ),
                    'gift_aid' =>
                        (bool) $legacy->giftAid,
                    'is_anonymous' =>
                        blank($legacy->donorName)
                        && blank($legacy->donorEmail),
                    'recorded_by_user_id' => null,
                    'donor_name' =>
                        $this->cleanText(
                            $legacy->donorName ?? null
                        ),
                    'donor_email' =>
                        $this->cleanText(
                            $legacy->donorEmail ?? null
                        ),
                    'donor_phone' => null,
                    'address_line_1' =>
                        $this->cleanText(
                            $legacy->address1 ?? null
                        ),
                    'address_line_2' =>
                        $this->cleanText(
                            $legacy->address2 ?? null
                        ),
                    'city' =>
                        $this->cleanText(
                            $legacy->city ?? null
                        ),
                    'county' =>
                        $this->cleanText(
                            $legacy->county ?? null
                        ),
                    'postcode' =>
                        $this->cleanText(
                            $legacy->postcode ?? null
                        ),
                    'country' =>
                        $this->normaliseCountry(
                            $legacy->country ?? null
                        ),
                    'status' =>
                        $this->normaliseDonationStatus(
                            $legacy->status ?? null
                        ),
                    'payment_provider' => 'stripe',
                    'stripe_session_id' =>
                        $legacy->stripeSessionId ?? null,
                    'stripe_payment_intent_id' =>
                        $legacy->stripePaymentIntentId
                            ?? null,
                    'stripe_subscription_id' =>
                        $legacy->stripeSubscriptionId
                            ?? null,
                    'stripe_customer_id' => null,
                    'paid_at' =>
                        $this->safeTimestamp(
                            $legacy->paidAt ?? null
                        ),
                    'failed_at' => null,
                    'cancelled_at' => null,
                    'failure_reason' => null,
                    'notes' =>
                        'Imported from OviBase.',
                    'provider_metadata' => [
                        'legacy_tenant_id' =>
                            $legacy->tenantId,
                        'legacy_created_at' =>
                            $legacy->createdAt,
                    ],
                    'legacy_ovibase_id' =>
                        (string) $legacy->id,
                    'legacy_tenant_id' =>
                        (string) $legacy->tenantId,
                ];

                if ($existing) {
                    if ($this->updateExisting) {
                        $this->stats[
                            'donations_updated'
                        ]++;

                        if (! $this->dryRun) {
                            $existing->update($payload);
                        }
                    }

                    continue;
                }

                if ($this->dryRun) {
                    $this->stats['donations_created']++;

                    continue;
                }

                $donation = Donation::query()
                    ->create($payload);

                DB::table('donations')
                    ->where('id', $donation->getKey())
                    ->update([
                        'created_at' =>
                            $this->safeTimestamp(
                                $legacy->createdAt ?? null
                            ) ?? now(),
                        'updated_at' =>
                            $this->safeTimestamp(
                                $legacy->createdAt ?? null
                            ) ?? now(),
                    ]);

                $this->stats['donations_created']++;
            } catch (Throwable $exception) {
                $this->stats['records_failed']++;

                $this->warn(
                    "Donation {$legacy->id} failed: "
                    . $exception->getMessage()
                );
            }
        }
    }

    private function importFinanceTransactions(): void
    {
        $records = DB::connection('ovibase')
            ->table($this->legacyTable('finance'))
            ->where('tenantId', self::TENANT_ID)
            ->orderBy('date')
            ->get();

        $this->stats['finance_records_found'] =
            $records->count();

        foreach ($records as $legacy) {
            try {
                $existing = FinanceTransaction::query()
                    ->where(
                        'legacy_ovibase_id',
                        (string) $legacy->id
                    )
                    ->first();

                $type = $this->normaliseFinanceType(
                    $legacy->type ?? null
                );

                if ($type === null) {
                    $this->stats[
                        'finance_transactions_skipped'
                    ]++;

                    continue;
                }

                $categoryName =
                    $this->cleanText(
                        $legacy->category ?? null
                    );

                $incomeCategoryId = null;
                $expenseCategoryId = null;

                if (
                    $type ===
                    FinanceTransaction::TYPE_INCOME
                ) {
                    $incomeCategoryId =
                        $this->resolveIncomeCategoryId(
                            $categoryName
                        );
                } else {
                    $expenseCategoryId =
                        $this->resolveExpenseCategoryId(
                            $categoryName
                        );
                }

                $donation = null;

                if (filled($legacy->donationId)) {
                    $donation = Donation::query()
                        ->where(
                            'legacy_ovibase_id',
                            (string) $legacy->donationId
                        )
                        ->first();
                }

                $payload = [
                    'type' => $type,
                    'income_category_id' =>
                        $incomeCategoryId,
                    'expense_category_id' =>
                        $expenseCategoryId,
                    'donation_id' =>
                        $donation?->getKey(),
                    'created_by_user_id' => null,
                    'amount' => $legacy->amount,
                    'currency' =>
                        $donation?->currency ?? 'GBP',
                    'transaction_date' =>
                        Carbon::parse(
                            $legacy->date
                        )->toDateString(),
                    'description' =>
                        $this->cleanText(
                            $legacy->description ?? null
                        ),
                    'notes' =>
                        'Imported from OviBase finance.',
                    'reference' =>
                        $donation
                            ? 'DON-' . str_pad(
                                (string) $donation->getKey(),
                                8,
                                '0',
                                STR_PAD_LEFT
                            )
                            : 'OVI-' . Str::upper(
                                Str::substr(
                                    (string) $legacy->id,
                                    -10
                                )
                            ),
                    'payment_method' =>
                        $donation
                            ? 'online_payment'
                            : null,
                    'source' =>
                        FinanceTransaction::SOURCE_IMPORT,
                    'status' =>
                        FinanceTransaction::STATUS_COMPLETED,
                    'legacy_category_name' =>
                        $categoryName,
                    'legacy_ovibase_id' =>
                        (string) $legacy->id,
                    'legacy_tenant_id' =>
                        (string) $legacy->tenantId,
                ];

                if ($existing) {
                    if ($this->updateExisting) {
                        $this->stats[
                            'finance_transactions_updated'
                        ]++;

                        if (! $this->dryRun) {
                            $existing->update($payload);
                        }
                    }

                    continue;
                }

                if ($this->dryRun) {
                    $this->stats[
                        'finance_transactions_created'
                    ]++;

                    continue;
                }

                $transaction =
                    FinanceTransaction::query()
                        ->create($payload);

                DB::table('finance_transactions')
                    ->where(
                        'id',
                        $transaction->getKey()
                    )
                    ->update([
                        'created_at' =>
                            $this->safeTimestamp(
                                $legacy->createdAt ?? null
                            ) ?? now(),
                        'updated_at' =>
                            $this->safeTimestamp(
                                $legacy->updatedAt ?? null
                            ) ?? now(),
                    ]);

                $this->stats[
                    'finance_transactions_created'
                ]++;
            } catch (Throwable $exception) {
                $this->stats['records_failed']++;

                $this->warn(
                    "Finance record {$legacy->id} failed: "
                    . $exception->getMessage()
                );
            }
        }
    }

    /**
     * Discover legacy table names using the exact casing returned by MySQL.
     *
     * Windows commonly treats MySQL table names case-insensitively, while
     * Linux normally does not. Mapping them once makes the importer portable.
     *
     * @return array<string, string>
     */
    private function discoverLegacyTables(): array
    {
        $database = DB::connection('ovibase')
            ->getDatabaseName();

        $rows = DB::connection('ovibase')
            ->select('SHOW TABLES');

        $column = 'Tables_in_' . $database;
        $tables = [];

        foreach ($rows as $row) {
            $values = (array) $row;
            $actualName = $values[$column]
                ?? reset($values);

            if (! is_string($actualName)) {
                continue;
            }

            $tables[Str::lower($actualName)] =
                $actualName;
        }

        return $tables;
    }

    private function legacyTable(
        string $logicalName
    ): string {
        $key = Str::lower($logicalName);

        if (! isset($this->legacyTables[$key])) {
            throw new \RuntimeException(
                "The OviBase {$logicalName} table was not resolved."
            );
        }

        return $this->legacyTables[$key];
    }

    private function resolveDefaultFund(): ?DonationFund
    {
        $requested = trim(
            (string) $this->option(
                'default-fund'
            )
        );

        return DonationFund::query()
            ->whereRaw(
                'LOWER(name) = ?',
                [Str::lower($requested)]
            )
            ->first()
            ?? DonationFund::query()
                ->where('is_default', true)
                ->first()
            ?? DonationFund::query()
                ->ordered()
                ->first();
    }

    private function resolveIncomeCategoryId(
        ?string $name
    ): ?int {
        if (blank($name)) {
            return null;
        }

        $category = IncomeCategory::query()
            ->whereRaw(
                'LOWER(name) = ?',
                [Str::lower($name)]
            )
            ->first();

        if ($category) {
            return (int) $category->getKey();
        }

        if ($this->dryRun) {
            return null;
        }

        return (int) IncomeCategory::query()
            ->create([
                'name' => $name,
                'description' =>
                    'Created during OviBase import.',
                'is_active' => true,
                'sort_order' => 0,
            ])
            ->getKey();
    }

    private function resolveExpenseCategoryId(
        ?string $name
    ): ?int {
        if (blank($name)) {
            return null;
        }

        $category = ExpenseCategory::query()
            ->whereRaw(
                'LOWER(name) = ?',
                [Str::lower($name)]
            )
            ->first();

        if ($category) {
            return (int) $category->getKey();
        }

        if ($this->dryRun) {
            return null;
        }

        return (int) ExpenseCategory::query()
            ->create([
                'name' => $name,
                'description' =>
                    'Created during OviBase import.',
                'is_active' => true,
                'sort_order' => 0,
            ])
            ->getKey();
    }

    private function normaliseDonationStatus(
        mixed $status
    ): string {
        return match (
            Str::upper(trim((string) $status))
        ) {
            'PAID' => Donation::STATUS_PAID,
            'FAILED' => Donation::STATUS_FAILED,
            'CANCELED',
            'CANCELLED' =>
                Donation::STATUS_CANCELLED,
            default => Donation::STATUS_PENDING,
        };
    }

    private function normaliseFinanceType(
        mixed $type
    ): ?string {
        return match (
            Str::lower(trim((string) $type))
        ) {
            'income' =>
                FinanceTransaction::TYPE_INCOME,
            'expense' =>
                FinanceTransaction::TYPE_EXPENSE,
            default => null,
        };
    }

    private function normaliseInterval(
        mixed $interval
    ): ?string {
        $value = Str::lower(
            trim((string) $interval)
        );

        return match ($value) {
            'weekly', 'week' => 'weekly',
            'monthly', 'month' => 'monthly',
            'yearly', 'annual', 'annually',
            'year' => 'yearly',
            default => null,
        };
    }

    private function normaliseCountry(
        mixed $country
    ): ?string {
        $value = Str::upper(
            trim((string) $country)
        );

        return match ($value) {
            'GB', 'UK' => 'United Kingdom',
            'US' => 'United States',
            '' => null,
            default => $value,
        };
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

    private function cleanText(
        mixed $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        $clean = trim((string) $value);

        return $clean === ''
            ? null
            : $clean;
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

        $this->newLine();

        if ($this->dryRun) {
            $this->warn(
                'Dry run complete. No records were written.'
            );

            $this->line(
                'Run without --dry-run when the totals look correct.'
            );
        } else {
            $this->info(
                'OviBase finance import completed.'
            );

            $this->line(
                'The command may be safely rerun because legacy IDs prevent duplicates.'
            );
        }
    }
}
