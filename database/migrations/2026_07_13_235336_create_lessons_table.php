<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('slug');

            $table->text('summary')
                ->nullable();

            $table->longText('content')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Video
            |--------------------------------------------------------------------------
            |
            | We will store only a YouTube or Vimeo URL. Videos will not be
            | uploaded to the VPS.
            |
            */

            $table->string('video_provider')
                ->nullable();

            $table->text('video_url')
                ->nullable();

            $table->unsignedInteger('video_duration_minutes')
                ->nullable();

            /*
            |--------------------------------------------------------------------------
            | Learning controls
            |--------------------------------------------------------------------------
            */

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->boolean('is_preview')
                ->default(false);

            $table->boolean('is_published')
                ->default(true);

            $table->boolean('requires_manual_completion')
                ->default(true);

            $table->timestamps();

            $table->unique([
                'course_id',
                'slug',
            ]);

            $table->index([
                'course_id',
                'sort_order',
            ]);

            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};