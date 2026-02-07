<?php
//gimscare-upgrade/database/migrations/2026_01_25_074016_create_job_listings_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();

            $table->string('location')->nullable();
            $table->string('employment_type')->nullable();
            $table->string('salary')->nullable();
            $table->date('closing_date')->nullable();

            $table->enum('template', ['adult_carer', 'children_carer'])
                ->default('adult_carer');

            $table->longText('description')->nullable();
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
