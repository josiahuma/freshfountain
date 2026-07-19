<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\AttendanceServiceType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AttendanceHistorical2026Seeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['New Years Eve', '2025-12-31', 30, 42, 27],

            ['Sunday Encounter', '2026-01-04', 26, 23, 20],
            ['Sunday Encounter', '2026-01-11', 28, 22, 21],
            ['Sunday Encounter', '2026-01-18', 27, 32, 23],
            ['Sunday Encounter', '2026-01-25', 26, 30, 28],

            ['Sunday Encounter', '2026-02-01', 27, 31, 22],
            ['Sunday Encounter', '2026-02-08', 30, 25, 19],
            ['Sunday Encounter', '2026-02-15', 27, 28, 22],

            ['Aflame Conference', '2026-02-27', 22, 26, 21],
            ['Aflame Conference', '2026-02-28', 24, 23, 22],

            ['Refresh', '2026-03-06', 16, 14, 10],
            ['Sunday Encounter', '2026-03-08', 31, 24, 26],
            ['Sunday Encounter', '2026-03-15', 29, 33, 22],
            ['Sunday Encounter', '2026-03-22', 24, 28, 30],
            ['Sunday Encounter', '2026-03-29', 31, 28, 29],

            ['Third Anniversary Service', '2026-04-03', 35, 38, 27],
            ['Sunday Encounter', '2026-04-05', 49, 45, 28],
            ['Sunday Encounter', '2026-04-12', 24, 21, 22],
            ['Sunday Encounter', '2026-04-26', 24, 23, 31],

            ['Refresh', '2026-05-01', 21, 20, 9],
            ['Sunday Encounter', '2026-05-03', 75, 97, 45],
            ['Sunday Encounter', '2026-05-10', 29, 25, 26],
            ['Sunday Encounter', '2026-05-24', 27, 32, 32],
            ['Sunday Encounter', '2026-05-31', 23, 35, 29],

            ['Refresh', '2026-06-05', 14, 18, 13],
            ['Sunday Encounter', '2026-06-07', 41, 33, 32],
            ['Sunday Encounter', '2026-06-14', 30, 31, 32],
            ['Sunday Encounter', '2026-06-21', 27, 29, 22],
            ['Sunday Encounter', '2026-06-28', 24, 23, 24],

            ['Sunday Encounter', '2026-07-05', 30, 25, 21],
            ['Sunday Encounter', '2026-07-12', 34, 36, 26],
        ];

        $created = 0;
        $updated = 0;

        foreach ($records as [$serviceName, $date, $men, $women, $children]) {
            $serviceType = AttendanceServiceType::query()
                ->whereRaw('LOWER(name) = ?', [Str::lower($serviceName)])
                ->first();

            if (! $serviceType) {
                $serviceType = AttendanceServiceType::query()->create([
                    'name' => $serviceName,
                    'is_active' => true,
                    'sort_order' => 999,
                ]);
            }

            $attendance = Attendance::query()->firstOrNew([
                'service_type_id' => $serviceType->id,
                'service_date' => $date,
            ]);

            $wasExisting = $attendance->exists;

            $attendance->fill([
                'service_name' => $serviceType->name,
                'men' => $men,
                'women' => $women,
                'children' => $children,
                'visitors' => 0,
                'online' => 0,
                'notes' => 'Imported from historical paper attendance register.',
            ]);

            $attendance->save();

            $wasExisting ? $updated++ : $created++;
        }

        $this->command?->table(
            ['Result', 'Count'],
            [
                ['Created', $created],
                ['Updated', $updated],
                ['Total processed', count($records)],
            ],
        );
    }
}