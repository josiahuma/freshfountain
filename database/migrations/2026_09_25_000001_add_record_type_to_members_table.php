<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->string('record_type', 30)
                ->default('person')
                ->after('user_id')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table): void {
            $table->dropIndex(['record_type']);
            $table->dropColumn('record_type');
        });
    }
};
