<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'quiz_attempt_answers',
            function (Blueprint $table): void {
                $table->json('selected_answer_ids')
                    ->nullable()
                    ->after('quiz_answer_id');
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'quiz_attempt_answers',
            function (Blueprint $table): void {
                $table->dropColumn(
                    'selected_answer_ids'
                );
            }
        );
    }
};