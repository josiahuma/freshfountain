<?php

namespace App\Console\Commands;

use App\Models\TransportBooking;
use App\Models\TransportPickupEvent;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class ImportOvipointTransport extends Command
{
    protected $signature = 'ovipoint:import-transport
        {--church= : Legacy Ovipoint church ID; defaults to OVIPOINT_CHURCH_ID}
        {--dry-run : Analyse without writing records}';

    protected $description = 'Import Fresh Fountain pickup events and bookings from the legacy Ovipoint database.';

    public function handle(): int
    {
        $churchId = (int) ($this->option('church') ?: config('transport.ovipoint.church_id', 1));
        $dryRun = (bool) $this->option('dry-run');

        try {
            $this->configureLegacyConnection();

            $church = DB::connection('ovipoint')
                ->table('Church')
                ->where('id', $churchId)
                ->first();

            if (! $church) {
                $this->error("Ovipoint church ID {$churchId} was not found.");
                return self::FAILURE;
            }

            $legacyEvents = DB::connection('ovipoint')
                ->table('PickupEvent')
                ->where('churchId', $churchId)
                ->orderBy('pickupDate')
                ->orderBy('id')
                ->get();

            $legacyEventIds = $legacyEvents->pluck('id')->all();

            $legacyBookings = empty($legacyEventIds)
                ? collect()
                : DB::connection('ovipoint')
                    ->table('Booking')
                    ->whereIn('pickupEventId', $legacyEventIds)
                    ->orderBy('id')
                    ->get();

            $eventCreated = 0;
            $eventUpdated = 0;
            $eventSkipped = 0;
            $bookingCreated = 0;
            $bookingUpdated = 0;
            $bookingSkipped = 0;
            $failed = 0;

            $eventMap = [];

            foreach ($legacyEvents as $legacy) {
                try {
                    $values = [
                        'title' => trim((string) $legacy->title),
                        'pickup_date' => (string) $legacy->pickupDate,
                        'capacity_per_slot' => max(1, (int) $legacy->capacity),
                        'pickup_start_time' => (string) $legacy->pickupStartTime,
                        'pickup_end_time' => (string) $legacy->pickupEndTime,
                        'interval_minutes' => max(1, (int) $legacy->intervalMinutes),
                        'bookings_open' => (bool) $legacy->bookingsOpen,
                        'legacy_source' => 'ovipoint',
                        'legacy_id' => (string) $legacy->id,
                        'legacy_payload' => [
                            'church_id' => (int) $legacy->churchId,
                            'inserted_at' => $legacy->insertedAt,
                            'updated_at' => $legacy->updatedAt,
                        ],
                        'created_at' => $legacy->insertedAt,
                        'updated_at' => $legacy->updatedAt,
                    ];

                    $existing = TransportPickupEvent::query()
                        ->where('legacy_source', 'ovipoint')
                        ->where('legacy_id', (string) $legacy->id)
                        ->first();

                    if ($existing) {
                        $changed = collect($values)
                            ->except(['created_at'])
                            ->contains(fn ($value, $key): bool => $existing->{$key} != $value);

                        if (! $changed) {
                            $eventSkipped++;
                        } else {
                            $eventUpdated++;
                            if (! $dryRun) {
                                $existing->forceFill($values)->save();
                            }
                        }

                        $eventMap[(string) $legacy->id] = $existing->id;
                        continue;
                    }

                    $eventCreated++;

                    if (! $dryRun) {
                        $created = TransportPickupEvent::query()->create($values);
                        $eventMap[(string) $legacy->id] = $created->id;
                    }
                } catch (Throwable $exception) {
                    $failed++;
                    $this->warn("Event {$legacy->id} failed: {$exception->getMessage()}");
                }
            }

            if ($dryRun) {
                $existingMap = TransportPickupEvent::query()
                    ->where('legacy_source', 'ovipoint')
                    ->whereIn('legacy_id', collect($legacyEventIds)->map(fn ($id) => (string) $id)->all())
                    ->pluck('id', 'legacy_id')
                    ->all();

                $eventMap = array_replace($existingMap, $eventMap);
            }

            foreach ($legacyBookings as $legacy) {
                try {
                    $localEventId = $eventMap[(string) $legacy->pickupEventId] ?? null;

                    if (! $localEventId) {
                        if ($dryRun) {
                            // The linked event is also new in this dry-run, so the booking is valid.
                            $legacyEventExists = $legacyEvents->contains(
                                fn ($event): bool => (string) $event->id === (string) $legacy->pickupEventId
                            );

                            if (! $legacyEventExists) {
                                $failed++;
                                $this->warn("Booking {$legacy->id} references missing pickup event {$legacy->pickupEventId}.");
                                continue;
                            }
                        } else {
                            $failed++;
                            $this->warn("Booking {$legacy->id} references a pickup event that was not imported.");
                            continue;
                        }
                    }

                    $values = [
                        'name' => trim((string) $legacy->name),
                        'phone' => trim((string) $legacy->phone),
                        'address' => trim((string) $legacy->address),
                        'pickup_time' => (string) $legacy->pickupTime,
                        'party_size' => max(1, (int) $legacy->partySize),
                        'status' => TransportBooking::STATUS_CONFIRMED,
                        'legacy_source' => 'ovipoint',
                        'legacy_id' => (string) $legacy->id,
                        'legacy_payload' => [
                            'pickup_event_id' => (int) $legacy->pickupEventId,
                            'event_date_id' => $legacy->eventDateId,
                            'inserted_at' => $legacy->insertedAt,
                            'updated_at' => $legacy->updatedAt,
                        ],
                        'created_at' => $legacy->insertedAt,
                        'updated_at' => $legacy->updatedAt,
                    ];

                    if ($localEventId) {
                        $values['transport_pickup_event_id'] = $localEventId;
                    }

                    $existing = TransportBooking::query()
                        ->where('legacy_source', 'ovipoint')
                        ->where('legacy_id', (string) $legacy->id)
                        ->first();

                    if ($existing) {
                        $compareValues = $values;
                        unset($compareValues['created_at']);

                        $changed = collect($compareValues)
                            ->contains(fn ($value, $key): bool => $existing->{$key} != $value);

                        if (! $changed) {
                            $bookingSkipped++;
                            continue;
                        }

                        $bookingUpdated++;
                        if (! $dryRun) {
                            $existing->forceFill($values)->save();
                        }
                        continue;
                    }

                    $bookingCreated++;
                    if (! $dryRun) {
                        TransportBooking::query()->create($values);
                    }
                } catch (Throwable $exception) {
                    $failed++;
                    $this->warn("Booking {$legacy->id} failed: {$exception->getMessage()}");
                }
            }

            $this->newLine();
            $this->info($dryRun ? 'Ovipoint transport import — DRY RUN' : 'Importing Ovipoint transport data');
            $this->line("Church: {$church->name} (legacy ID {$churchId})");
            $this->newLine();

            $this->table(['Result', 'Count'], [
                ['Pickup events found', $legacyEvents->count()],
                [$dryRun ? 'Pickup events would create' : 'Pickup events created', $eventCreated],
                [$dryRun ? 'Pickup events would update' : 'Pickup events updated', $eventUpdated],
                ['Pickup events unchanged / skipped', $eventSkipped],
                ['Bookings found', $legacyBookings->count()],
                [$dryRun ? 'Bookings would create' : 'Bookings created', $bookingCreated],
                [$dryRun ? 'Bookings would update' : 'Bookings updated', $bookingUpdated],
                ['Bookings unchanged / skipped', $bookingSkipped],
                ['Records failed', $failed],
            ]);

            if ($dryRun) {
                $this->warn('Dry run complete. No records were written.');
            } else {
                $this->info('Ovipoint transport import completed. Legacy IDs prevent duplicate imports.');
            }

            return $failed > 0 ? self::FAILURE : self::SUCCESS;
        } catch (Throwable $exception) {
            report($exception);
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }

    private function configureLegacyConnection(): void
    {
        $legacy = config('transport.ovipoint');

        config([
            'database.connections.ovipoint' => [
                'driver' => 'mysql',
                'host' => $legacy['host'] ?? '127.0.0.1',
                'port' => $legacy['port'] ?? '3306',
                'database' => $legacy['database'] ?? null,
                'username' => $legacy['username'] ?? null,
                'password' => $legacy['password'] ?? null,
                'unix_socket' => $legacy['socket'] ?? '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'prefix_indexes' => true,
                'strict' => true,
                'engine' => null,
            ],
        ]);

        DB::purge('ovipoint');
    }
}
