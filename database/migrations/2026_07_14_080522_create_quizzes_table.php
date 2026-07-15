<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lesson_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->unsignedTinyInteger('pass_percentage')
                ->default(80);

            $table->unsignedInteger('maximum_attempts')
                ->nullable();

            $table->boolean('is_published')
                ->default(true);

            $table->boolean('is_required')
                ->default(true);

            $table->boolean('shuffle_questions')
                ->default(false);

            $table->boolean('shuffle_answers')
                ->default(false);

            $table->boolean('show_correct_answers')
                ->default(true);

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->timestamps();

            $table->unique('lesson_id');

            $table->index('is_published');
            $table->index('is_required');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};