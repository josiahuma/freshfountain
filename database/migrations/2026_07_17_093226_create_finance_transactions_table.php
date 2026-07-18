<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'finance_transactions',
            function (Blueprint $table): void {
                $table->id();

                $table->string('type', 20);

                $table->foreignId('income_category_id')
                    ->nullable()
                    ->constrained('income_categories')
                    ->nullOnDelete();

                $table->foreignId('expense_category_id')
                    ->nullable()
                    ->constrained('expense_categories')
                    ->nullOnDelete();

                $table->foreignId('donation_id')
                    ->nullable()
                    ->constrained('donations')
                    ->nullOnDelete();

                $table->foreignId('created_by_user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->decimal('amount', 12, 2);

                $table->string('currency', 3)
                    ->default('GBP');

                $table->date('transaction_date');

                $table->string('description')
                    ->nullable();

                $table->text('notes')
                    ->nullable();

                $table->string('reference')
                    ->nullable();

                $table->string('payment_method', 50)
                    ->nullable();

                $table->string('source', 30)
                    ->default('manual');

                $table->string('status', 30)
                    ->default('completed');

                $table->string('legacy_category_name')
                    ->nullable();

                $table->string('legacy_ovibase_id', 30)
                    ->nullable()
                    ->unique();

                $table->string('legacy_tenant_id', 30)
                    ->nullable()
                    ->index();

                $table->timestamps();

                $table->index([
                    'type',
                    'transaction_date',
                ]);

                $table->index([
                    'source',
                    'transaction_date',
                ]);

                $table->index([
                    'status',
                    'transaction_date',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};