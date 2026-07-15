<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('status')
                ->default('active');

            $table->timestamp('enrolled_at')
                ->nullable();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('completed_at')
                ->nullable();

            $table->timestamp('last_activity_at')
                ->nullable();

            $table->unsignedInteger('progress_percentage')
                ->default(0);

            $table->foreignId('last_lesson_id')
                ->nullable()
                ->constrained('lessons')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique([
                'course_id',
                'user_id',
            ]);

            $table->index('status');
            $table->index('progress_percentage');
            $table->index('last_activity_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};