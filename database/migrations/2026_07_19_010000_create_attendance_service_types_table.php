<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_service_types', function (Blueprint $table): void {
            $table->id();
            $table->string('name', 191)->unique();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('attendances', function (Blueprint $table): void {
            $table->foreignId('service_type_id')
                ->nullable()
                ->after('id')
                ->constrained('attendance_service_types')
                ->nullOnDelete();
        });

        $names = DB::table('attendances')
            ->whereNotNull('service_name')
            ->where('service_name', '<>', '')
            ->select('service_name')
            ->distinct()
            ->orderBy('service_name')
            ->pluck('service_name');

        $defaults = collect([
            'Sunday Encounter',
            'Refresh',
            'Prayer Stretch',
            'Youth Service',
            'Communion Service',
            'Thanksgiving Service',
            'Crossover Service',
            'Easter Service',
            'Christmas Service',
        ]);

        $allNames = $names
            ->merge($defaults)
            ->map(fn (mixed $name): string => trim((string) $name))
            ->filter()
            ->unique(fn (string $name): string => mb_strtolower($name))
            ->values();

        foreach ($allNames as $index => $name) {
            DB::table('attendance_service_types')->insert([
                'name' => $name,
                'is_active' => true,
                'sort_order' => ($index + 1) * 10,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('attendance_service_types')
            ->orderBy('id')
            ->get(['id', 'name'])
            ->each(function (object $serviceType): void {
                DB::table('attendances')
                    ->whereRaw('LOWER(TRIM(service_name)) = ?', [mb_strtolower(trim($serviceType->name))])
                    ->update(['service_type_id' => $serviceType->id]);
            });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('service_type_id');
        });

        Schema::dropIfExists('attendance_service_types');
    }
};
