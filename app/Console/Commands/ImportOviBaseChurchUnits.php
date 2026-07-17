<?php

namespace App\Console\Commands;

use App\Models\ChurchUnit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class ImportOviBaseChurchUnits extends Command
{
    protected $signature =
        'ovibase:import-units
        {--dry-run : Analyse without writing records}';

    protected $description =
        'Import Fresh Fountain church units from the OviBase legacy database.';

    private const TENANT_ID =
        'cmjne5xfj0000ak3n11khqh89';

    public function handle(): int
    {
        $dryRun = (bool) $this->option(
            'dry-run'
        );

        $this->info(
            $dryRun
                ? 'OviBase church-unit import dry run'
                : 'Importing OviBase church units'
        );

        try {
            $legacyUnits = DB::connection(
                'ovibase'
            )
                ->table(
                    'ChurchUnitCategory'
                )
                ->where(
                    'tenantId',
                    self::TENANT_ID
                )
                ->orderBy('createdAt')
                ->get();

            $created = 0;
            $matched = 0;

            foreach ($legacyUnits as $legacy) {
                $existing = ChurchUnit::query()
                    ->where(
                        'legacy_source',
                        'ovibase'
                    )
                    ->where(
                        'legacy_id',
                        $legacy->id
                    )
                    ->first();

                if (! $existing) {
                    $existing = ChurchUnit::query()
                        ->whereRaw(
                            'LOWER(name) = ?',
                            [
                                Str::lower(
                                    trim(
                                        $legacy->name
                                    )
                                ),
                            ]
                        )
                        ->first();
                }

                if ($existing) {
                    $matched++;

                    if (! $dryRun) {
                        $existing->update([
                            'legacy_source' =>
                                'ovibase',

                            'legacy_id' =>
                                $legacy->id,

                            'legacy_payload' => [
                                'tenant_id' =>
                                    $legacy->tenantId,

                                'created_at' =>
                                    $legacy->createdAt,

                                'updated_at' =>
                                    $legacy->updatedAt,
                            ],
                        ]);
                    }

                    continue;
                }

                $created++;

                if ($dryRun) {
                    continue;
                }

                ChurchUnit::query()->create([
                    'name' =>
                        trim($legacy->name),

                    'slug' =>
                        Str::slug(
                            $legacy->name
                        ),

                    'alias' =>
                        trim(
                            (string)
                            $legacy->alias
                        ) ?: null,

                    'sort_order' =>
                        $created,

                    'is_active' =>
                        true,

                    'legacy_source' =>
                        'ovibase',

                    'legacy_id' =>
                        $legacy->id,

                    'legacy_payload' => [
                        'tenant_id' =>
                            $legacy->tenantId,

                        'created_at' =>
                            $legacy->createdAt,

                        'updated_at' =>
                            $legacy->updatedAt,
                    ],

                    'created_at' =>
                        $legacy->createdAt,

                    'updated_at' =>
                        $legacy->updatedAt,
                ]);
            }

            $this->table(
                ['Result', 'Count'],
                [
                    ['Legacy units found', $legacyUnits->count()],
                    ['Would create / created', $created],
                    ['Matched existing', $matched],
                ]
            );

            if ($dryRun) {
                $this->warn(
                    'Dry run complete. No records were written.'
                );
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }
}