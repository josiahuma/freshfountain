<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'quiz_attempts',
            function (Blueprint $table): void {
                $table->string('legacy_source', 50)
                    ->nullable()
                    ->after('id');

                $table->unsignedBigInteger('legacy_id')
                    ->nullable()
                    ->after('legacy_source');

                $table->unique(
                    [
                        'legacy_source',
                        'legacy_id',
                    ],
                    'quiz_attempts_legacy_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'quiz_attempts',
            function (Blueprint $table): void {
                $table->dropUnique(
                    'quiz_attempts_legacy_unique'
                );

                $table->dropColumn([
                    'legacy_source',
                    'legacy_id',
                ]);
            }
        );
    }
};