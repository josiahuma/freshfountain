<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('church_units', function (Blueprint $table): void {
            $table->text('short_description')->nullable()->after('alias');
            $table->string('feature_image')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('church_units', function (Blueprint $table): void {
            $table->dropColumn(['short_description', 'feature_image']);
        });
    }
};
