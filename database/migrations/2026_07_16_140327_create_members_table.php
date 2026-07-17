<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table): void {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | Optional account and church-unit links
            |--------------------------------------------------------------------------
            */

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('church_unit_id')
                ->nullable()
                ->constrained('church_units')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | Personal details
            |--------------------------------------------------------------------------
            */

            $table->string('title', 30)
                ->nullable();

            $table->string('first_name');

            $table->string('middle_name')
                ->nullable();

            $table->string('last_name')
                ->nullable();

            $table->string('gender', 30)
                ->nullable();

            $table->date('date_of_birth')
                ->nullable();

            $table->date('anniversary_date')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Contact details
            |--------------------------------------------------------------------------
            */

            $table->string('email')
                ->nullable();

            $table->string('mobile_number', 50)
                ->nullable();

            $table->string('alternative_phone', 50)
                ->nullable();

            $table->text('address')
                ->nullable();

            $table->string('postcode', 30)
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Church information
            |--------------------------------------------------------------------------
            */

            $table->string('membership_status', 40)
                ->default('active');

            $table->date('joined_at')
                ->nullable();

            $table->string('legacy_church_leader_name')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            /*
            |--------------------------------------------------------------------------
            | Communication preferences
            |--------------------------------------------------------------------------
            */

            $table->boolean('email_consent')
                ->default(true);

            $table->boolean('sms_consent')
                ->default(true);

            $table->boolean('do_not_contact')
                ->default(false);

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
                'members_legacy_unique'
            );

            $table->index('email');
            $table->index('mobile_number');
            $table->index('date_of_birth');
            $table->index('anniversary_date');
            $table->index('membership_status');
            $table->index('is_active');

            $table->index(
                [
                    'church_unit_id',
                    'is_active',
                ]
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};