<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaders', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('member_id')
                ->nullable()
                ->unique()
                ->constrained('members')
                ->nullOnDelete();

            $table->foreignId('church_unit_id')
                ->nullable()
                ->constrained('church_units')
                ->nullOnDelete();

            $table->string('first_name');

            $table->string('middle_name')
                ->nullable();

            $table->string('last_name')
                ->nullable();

            $table->string('email')
                ->nullable();

            $table->string('mobile_number', 50)
                ->nullable();

            $table->string('leadership_role')
                ->default('Unit Leader');

            $table->date('started_at')
                ->nullable();

            $table->date('ended_at')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->text('notes')
                ->nullable();

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
                'leaders_legacy_unique'
            );

            $table->index('email');
            $table->index('mobile_number');
            $table->index('is_active');

            $table->index([
                'church_unit_id',
                'is_active',
            ]);
        });

        Schema::table('members', function (Blueprint $table): void {
            $table->foreignId('leader_id')
                ->nullable()
                ->after('church_unit_id')
                ->constrained('leaders')
                ->nullOnDelete();

            $table->index('leader_id');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropForeign([
                'leader_id',
            ]);

            $table->dropIndex([
                'leader_id',
            ]);

            $table->dropColumn('leader_id');
        });

        Schema::dropIfExists('leaders');
    }
};