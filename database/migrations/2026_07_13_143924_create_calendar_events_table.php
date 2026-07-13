<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calendar_events', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Basic event information
            |--------------------------------------------------------------------------
            */

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->string('location')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Date and time
            |--------------------------------------------------------------------------
            */

            $table->dateTime('starts_at');

            $table->dateTime('ends_at')
                ->nullable();

            $table->boolean('is_all_day')
                ->default(false);

            /*
            |--------------------------------------------------------------------------
            | Event presentation
            |--------------------------------------------------------------------------
            */

            $table->string('category')
                ->default('general');

            $table->string('colour')
                ->default('#1d4ed8');

            $table->string('image')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Event links and sources
            |--------------------------------------------------------------------------
            */

            $table->string('external_url')
                ->nullable();

            $table->string('source')
                ->default('internal');

            $table->string('eventib_event_id')
                ->nullable()
                ->unique();

            /*
            |--------------------------------------------------------------------------
            | Publishing controls
            |--------------------------------------------------------------------------
            */

            $table->boolean('is_featured')
                ->default(false);

            $table->boolean('is_published')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Recurring event preparation
            |--------------------------------------------------------------------------
            |
            | Recurrence will be activated later. Adding these fields now means
            | we will not need to restructure the table when we implement it.
            |
            */

            $table->string('recurrence_type')
                ->default('none');

            $table->date('recurrence_until')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Ordering
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | Indexes
            |--------------------------------------------------------------------------
            */

            $table->index('starts_at');
            $table->index('ends_at');
            $table->index('category');
            $table->index('source');
            $table->index('is_published');
            $table->index(['is_published', 'starts_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendar_events');
    }
};