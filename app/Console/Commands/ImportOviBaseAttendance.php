<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportOviBaseAttendance extends Command
{
    protected $signature = 'ovibase:import-attendance
        {--tenant=cmjne5xfj0000ak3n11khqh89 : OviBase tenant ID}
        {--dry-run : Analyse without writing records}';

    protected $description = 'Import numbers-only historical attendance from the OviBase database connection.';

    public function handle(): int
    {
        $tenantId = (string) $this->option('tenant');
        $dryRun = (bool) $this->option('dry-run');

        try {
            $legacyRows = DB::connection('ovibase')
                ->table('Attendance')
                ->where('tenantId', $tenantId)
                ->orderBy('date')
                ->get();

            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($legacyRows as $legacy) {
                $values = [
                    'service_name' => trim((string) $legacy->event),
                    'service_date' => Carbon::parse($legacy->date)->toDateString(),
                    'men' => max(0, (int) $legacy->men),
                    'women' => max(0, (int) $legacy->women),
                    'children' => max(0, (int) $legacy->children),
                    'visitors' => 0,
                    'online' => 0,
                    'legacy_source' => 'ovibase',
                    'legacy_id' => (string) $legacy->id,
                    'legacy_payload' => [
                        'tenant_id' => $legacy->tenantId,
                        'legacy_total' => (int) $legacy->total,
                        'created_at' => $legacy->createdAt,
                        'updated_at' => $legacy->updatedAt,
                    ],
                    'created_at' => $legacy->createdAt,
                    'updated_at' => $legacy->updatedAt,
                ];

                $existing = Attendance::query()
                    ->where('legacy_source', 'ovibase')
                    ->where('legacy_id', $legacy->id)
                    ->first();

                if ($existing) {
                    $changed = collect($values)->except(['created_at'])->contains(
                        fn ($value, $key): bool => $existing->{$key} != $value
                    );

                    if (! $changed) {
                        $skipped++;
                        continue;
                    }

                    $updated++;
                    if (! $dryRun) {
                        $existing->forceFill($values)->save();
                    }
                    continue;
                }

                $created++;
                if (! $dryRun) {
                    Attendance::query()->create($values);
                }
            }

            $this->table(['Result', 'Count'], [
                ['Legacy records found', $legacyRows->count()],
                [$dryRun ? 'Would create' : 'Created', $created],
                [$dryRun ? 'Would update' : 'Updated', $updated],
                ['Unchanged / skipped', $skipped],
            ]);

            if ($dryRun) {
                $this->warn('Dry run complete. No records were written.');
            }

            return self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }
}
