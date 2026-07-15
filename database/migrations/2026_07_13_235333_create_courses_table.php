<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            $table->string('title');

            $table->string('slug')
                ->unique();

            $table->text('short_description')
                ->nullable();

            $table->longText('description')
                ->nullable();

            $table->string('cover_image')
                ->nullable();

            $table->string('course_type')
                ->default('general');

            $table->string('difficulty_level')
                ->default('beginner');

            $table->unsignedInteger('estimated_duration_minutes')
                ->nullable();

            $table->boolean('is_published')
                ->default(false);

            $table->boolean('is_featured')
                ->default(false);

            $table->boolean('allow_self_enrolment')
                ->default(true);

            $table->boolean('requires_sequential_progress')
                ->default(true);

            $table->boolean('certificate_enabled')
                ->default(false);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamp('published_at')
                ->nullable();

            $table->timestamps();

            $table->index('course_type');
            $table->index('is_published');
            $table->index('is_featured');
            $table->index(['is_published', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};