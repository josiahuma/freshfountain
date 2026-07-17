<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'unit_membership_requests',
            function (Blueprint $table): void {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | CRM relationships
                |--------------------------------------------------------------------------
                */

                $table->foreignId('member_id')
                    ->nullable()
                    ->constrained('members')
                    ->nullOnDelete();

                $table->foreignId('church_unit_id')
                    ->constrained('church_units')
                    ->cascadeOnDelete();

                $table->foreignId('assigned_leader_id')
                    ->nullable()
                    ->constrained('leaders')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Submitted contact details
                |--------------------------------------------------------------------------
                */

                $table->string('first_name');

                $table->string('last_name')
                    ->nullable();

                $table->string('email')
                    ->nullable();

                $table->string('mobile_number', 50)
                    ->nullable();

                $table->text('message')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Workflow
                |--------------------------------------------------------------------------
                */

                $table->string('status', 40)
                    ->default('pending');

                $table->timestamp('submitted_at')
                    ->nullable();

                $table->timestamp('assigned_at')
                    ->nullable();

                $table->timestamp('contacted_at')
                    ->nullable();

                $table->timestamp('reviewed_at')
                    ->nullable();

                $table->timestamp('approved_at')
                    ->nullable();

                $table->timestamp('declined_at')
                    ->nullable();

                $table->timestamp('completed_at')
                    ->nullable();

                $table->foreignId('reviewed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->text('internal_notes')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Request and migration metadata
                |--------------------------------------------------------------------------
                */

                $table->string(
                    'submission_reference',
                    50
                )->unique();

                $table->string('source', 50)
                    ->default('website');

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
                    'unit_requests_legacy_unique'
                );

                $table->index([
                    'church_unit_id',
                    'status',
                ]);

                $table->index([
                    'assigned_leader_id',
                    'status',
                ]);

                $table->index([
                    'member_id',
                    'status',
                ]);

                $table->index('email');
                $table->index('mobile_number');
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'unit_membership_requests'
        );
    }
};