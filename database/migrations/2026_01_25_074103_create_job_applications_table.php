<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();

            // ✅ Correct FK name for a JobListing parent
            $table->foreignId('job_listing_id')
                ->constrained('job_listings')
                ->cascadeOnDelete();

            $table->string('full_name');
            $table->string('email');
            $table->string('phone')->nullable();

            $table->enum('status', ['new', 'reviewed', 'shortlisted', 'rejected'])->default('new');

            $table->json('answers');
            $table->timestamp('submitted_at')->nullable();

            $table->timestamps();

            $table->index(['job_listing_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
