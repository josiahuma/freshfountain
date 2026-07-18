<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            $table->foreignId('member_id')
                ->nullable()
                ->after('donation_fund_id')
                ->constrained('members')
                ->nullOnDelete();

            $table->foreignId('finance_transaction_id')
                ->nullable()
                ->after('member_id')
                ->constrained('finance_transactions')
                ->nullOnDelete();

            $table->string('payment_method', 50)
                ->nullable()
                ->after('currency');

            $table->boolean('is_anonymous')
                ->default(false)
                ->after('gift_aid');

            $table->foreignId('recorded_by_user_id')
                ->nullable()
                ->after('is_anonymous')
                ->constrained('users')
                ->nullOnDelete();

            $table->text('notes')
                ->nullable()
                ->after('failure_reason');

            $table->index([
                'member_id',
                'status',
            ]);

            $table->index([
                'payment_method',
                'status',
            ]);

            $table->index([
                'is_anonymous',
                'status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table): void {
            $table->dropIndex([
                'member_id',
                'status',
            ]);

            $table->dropIndex([
                'payment_method',
                'status',
            ]);

            $table->dropIndex([
                'is_anonymous',
                'status',
            ]);

            $table->dropForeign([
                'member_id',
            ]);

            $table->dropForeign([
                'finance_transaction_id',
            ]);

            $table->dropForeign([
                'recorded_by_user_id',
            ]);

            $table->dropColumn([
                'member_id',
                'finance_transaction_id',
                'payment_method',
                'is_anonymous',
                'recorded_by_user_id',
                'notes',
            ]);
        });
    }
};