<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transport_notification_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('transport_booking_id')
                ->constrained('transport_bookings')
                ->cascadeOnDelete();

            $table->string('channel', 32);
            $table->string('recipient', 64);

            $table->string('provider', 64);

            $table->string('status', 32);
            $table->string('provider_status', 100)->nullable();

            $table->string('provider_message_sid', 191)->nullable();

            $table->unsignedSmallInteger('response_code')->nullable();

            $table->text('error_message')->nullable();

            $table->json('payload')->nullable();

            $table->timestamp('sent_at')->nullable();

            $table->timestamps();

            $table->index(
                ['transport_booking_id', 'channel'],
                'transport_notification_booking_channel_idx'
            );

            $table->index(
                ['status', 'channel'],
                'transport_notification_status_channel_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transport_notification_logs');
    }
};