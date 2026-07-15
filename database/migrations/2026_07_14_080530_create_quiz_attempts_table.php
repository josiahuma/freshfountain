<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('quiz_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_enrollment_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->unsignedInteger('attempt_number')
                ->default(1);

            $table->unsignedInteger('score_points')
                ->default(0);

            $table->unsignedInteger('maximum_points')
                ->default(0);

            $table->unsignedTinyInteger('percentage')
                ->default(0);

            $table->boolean('passed')
                ->default(false);

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('submitted_at')
                ->nullable();

            $table->timestamps();

            $table->unique([
                'quiz_id',
                'user_id',
                'attempt_number',
            ]);

            $table->index([
                'quiz_id',
                'user_id',
            ]);

            $table->index('passed');
            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};