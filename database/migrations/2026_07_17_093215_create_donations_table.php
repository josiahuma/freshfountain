<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('donation_fund_id')
                ->nullable()
                ->constrained('donation_funds')
                ->nullOnDelete();

            $table->decimal('amount', 12, 2);

            $table->string('currency', 3)
                ->default('GBP');

            $table->boolean('is_recurring')
                ->default(false);

            $table->string('recurring_interval', 20)
                ->nullable();

            $table->boolean('gift_aid')
                ->default(false);

            $table->string('donor_name')
                ->nullable();

            $table->string('donor_email')
                ->nullable();

            $table->string('donor_phone', 50)
                ->nullable();

            $table->string('address_line_1')
                ->nullable();

            $table->string('address_line_2')
                ->nullable();

            $table->string('city')
                ->nullable();

            $table->string('county')
                ->nullable();

            $table->string('postcode', 32)
                ->nullable();

            $table->string('country', 2)
                ->nullable();

            $table->string('status', 30)
                ->default('pending');

            $table->string('payment_provider', 30)
                ->default('stripe');

            $table->string('stripe_session_id')
                ->nullable()
                ->unique();

            $table->string('stripe_payment_intent_id')
                ->nullable()
                ->unique();

            $table->string('stripe_subscription_id')
                ->nullable()
                ->index();

            $table->string('stripe_customer_id')
                ->nullable()
                ->index();

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamp('failed_at')
                ->nullable();

            $table->timestamp('cancelled_at')
                ->nullable();

            $table->text('failure_reason')
                ->nullable();

            $table->json('provider_metadata')
                ->nullable();

            $table->string('legacy_ovibase_id', 30)
                ->nullable()
                ->unique();

            $table->string('legacy_tenant_id', 30)
                ->nullable()
                ->index();

            $table->timestamps();

            $table->index([
                'status',
                'paid_at',
            ]);

            $table->index([
                'gift_aid',
                'status',
            ]);

            $table->index([
                'donation_fund_id',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};