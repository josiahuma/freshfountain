<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lesson_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_enrollment_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('completed_at');

            $table->timestamps();

            $table->unique([
                'lesson_id',
                'user_id',
            ]);

            $table->index('completed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_completions');
    }
};