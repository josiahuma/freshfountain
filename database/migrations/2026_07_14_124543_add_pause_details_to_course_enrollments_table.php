<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'course_enrollments',
            function (Blueprint $table): void {
                $table->text('pause_reason')
                    ->nullable()
                    ->after('status');

                $table->timestamp('paused_at')
                    ->nullable()
                    ->after('pause_reason');

                $table->foreignId('paused_by')
                    ->nullable()
                    ->after('paused_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'course_enrollments',
            function (Blueprint $table): void {
                $table->dropForeign([
                    'paused_by',
                ]);

                $table->dropColumn([
                    'pause_reason',
                    'paused_at',
                    'paused_by',
                ]);
            }
        );
    }
};