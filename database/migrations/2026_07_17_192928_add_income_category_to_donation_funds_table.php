<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'donation_funds',
            function (Blueprint $table): void {
                $table->foreignId('income_category_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('income_categories')
                    ->nullOnDelete()
                    ->index();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'donation_funds',
            function (Blueprint $table): void {
                $table->dropConstrainedForeignId('income_category_id');
            }
        );
    }
};