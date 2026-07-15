<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_email_logs', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_enrollment_id')
                ->nullable()
                ->constrained('course_enrollments')
                ->cascadeOnDelete();

            $table->foreignId('lesson_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('quiz_attempt_id')
                ->nullable()
                ->constrained('quiz_attempts')
                ->cascadeOnDelete();

            $table->foreignId('certificate_id')
                ->nullable()
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type', 80);

            $table->string('dedupe_key')
                ->unique();

            $table->timestamp('sent_at')
                ->nullable();

            $table->timestamp('failed_at')
                ->nullable();

            $table->text('error_message')
                ->nullable();

            $table->unsignedSmallInteger('attempts')
                ->default(0);

            $table->timestamps();

            $table->index([
                'type',
                'sent_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_email_logs');
    }
};