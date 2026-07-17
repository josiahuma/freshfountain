<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'church_unit_member',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('member_id')
                    ->constrained('members')
                    ->cascadeOnDelete();

                $table->foreignId('church_unit_id')
                    ->constrained('church_units')
                    ->cascadeOnDelete();

                $table->foreignId('assigned_leader_id')
                    ->nullable()
                    ->constrained('leaders')
                    ->nullOnDelete();

                $table->string('status', 40)
                    ->default('active');

                $table->string('source', 50)
                    ->default('manual');

                $table->date('joined_at')
                    ->nullable();

                $table->date('left_at')
                    ->nullable();

                $table->text('notes')
                    ->nullable();

                $table->timestamps();

                $table->unique(
                    [
                        'member_id',
                        'church_unit_id',
                    ],
                    'church_unit_member_unique'
                );

                $table->index([
                    'church_unit_id',
                    'status',
                ]);

                $table->index([
                    'assigned_leader_id',
                    'status',
                ]);
            }
        );

        /*
         * Backfill existing legacy/primary unit assignments.
         *
         * Every member currently linked through members.church_unit_id
         * is added to the new pivot table. The original column remains.
         */
        DB::table('members')
            ->whereNotNull('church_unit_id')
            ->orderBy('id')
            ->chunkById(
                100,
                function ($members): void {
                    $rows = [];

                    foreach ($members as $member) {
                        $rows[] = [
                            'member_id' =>
                                $member->id,

                            'church_unit_id' =>
                                $member->church_unit_id,

                            'assigned_leader_id' =>
                                $member->leader_id,

                            'status' =>
                                'active',

                            'source' =>
                                filled($member->legacy_source)
                                    ? $member->legacy_source
                                    : 'existing_crm',

                            'joined_at' =>
                                $member->joined_at,

                            'left_at' =>
                                null,

                            'notes' =>
                                null,

                            'created_at' =>
                                $member->created_at
                                ?? now(),

                            'updated_at' =>
                                $member->updated_at
                                ?? now(),
                        ];
                    }

                    DB::table(
                        'church_unit_member'
                    )->insertOrIgnore($rows);
                }
            );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'church_unit_member'
        );
    }
};