<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_bookings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('transport_pickup_event_id')
                ->constrained('transport_pickup_events')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('address');
            $table->time('pickup_time');
            $table->unsignedInteger('party_size')->default(1);
            $table->string('status')->default('confirmed');
            $table->text('notes')->nullable();
            $table->string('legacy_source', 50)->nullable()->index();
            $table->string('legacy_id', 100)->nullable();
            $table->json('legacy_payload')->nullable();
            $table->timestamps();

            $table->index(['transport_pickup_event_id', 'pickup_time']);
            $table->unique(['legacy_source', 'legacy_id'], 'transport_bookings_legacy_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_bookings');
    }
};
