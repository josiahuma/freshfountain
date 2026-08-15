<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_pickup_events', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->date('pickup_date');
            $table->unsignedInteger('capacity_per_slot')->default(10);
            $table->time('pickup_start_time');
            $table->time('pickup_end_time');
            $table->unsignedInteger('interval_minutes')->default(15);
            $table->boolean('bookings_open')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('legacy_source', 50)->nullable()->index();
            $table->string('legacy_id', 100)->nullable();
            $table->json('legacy_payload')->nullable();
            $table->timestamps();

            $table->index(['pickup_date', 'bookings_open']);
            $table->unique(['legacy_source', 'legacy_id'], 'transport_pickup_events_legacy_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_pickup_events');
    }
};
