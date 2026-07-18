<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'has_backend_access')) {
                $table->boolean('has_backend_access')
                    ->default(false)
                    ->after('is_admin');
            }
        });

        DB::table('users')
            ->where('is_admin', true)
            ->update(['has_backend_access' => true]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (Schema::hasColumn('users', 'has_backend_access')) {
                $table->dropColumn('has_backend_access');
            }
        });
    }
};
