<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('course_enrollment_id')
                ->constrained('course_enrollments')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('issued_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('certificate_number')
                ->unique();

            $table->uuid('verification_code')
                ->unique();

            $table->string('student_name');

            $table->string('course_title');

            $table->timestamp('issued_at');

            $table->string('pdf_path')
                ->nullable();

            $table->timestamp('revoked_at')
                ->nullable();

            $table->text('revocation_reason')
                ->nullable();

            $table->json('metadata')
                ->nullable();

            $table->timestamps();

            $table->unique(
                'course_enrollment_id',
                'certificates_enrollment_unique'
            );

            $table->index([
                'user_id',
                'course_id',
            ]);

            $table->index('issued_at');
            $table->index('revoked_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};