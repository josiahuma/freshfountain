<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('church_units', function (Blueprint $table): void {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Core identity
            |--------------------------------------------------------------------------
            */

            $table->string('name')
                ->unique();

            $table->string('slug')
                ->unique();

            $table->string('alias')
                ->nullable();

            $table->text('description')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Display and administration
            |--------------------------------------------------------------------------
            */

            $table->string('email')
                ->nullable();

            $table->string('phone', 50)
                ->nullable();

            $table->string('meeting_day', 50)
                ->nullable();

            $table->string('meeting_time', 50)
                ->nullable();

            $table->string('meeting_location')
                ->nullable();

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Legacy migration support
            |--------------------------------------------------------------------------
            */

            $table->string('legacy_source', 50)
                ->nullable();

            $table->string('legacy_id')
                ->nullable();

            $table->json('legacy_payload')
                ->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'legacy_source',
                    'legacy_id',
                ],
                'church_units_legacy_unique'
            );

            $table->index([
                'is_active',
                'sort_order',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('church_units');
    }
};