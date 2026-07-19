<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->string('service_name', 191);
            $table->date('service_date');
            $table->unsignedInteger('men')->default(0);
            $table->unsignedInteger('women')->default(0);
            $table->unsignedInteger('children')->default(0);
            $table->unsignedInteger('visitors')->default(0);
            $table->unsignedInteger('online')->default(0);
            $table->unsignedInteger('total')->default(0);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('legacy_source', 50)->nullable();
            $table->string('legacy_id', 64)->nullable();
            $table->json('legacy_payload')->nullable();
            $table->timestamps();

            $table->index(['service_date', 'service_name']);
            $table->unique(['legacy_source', 'legacy_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
