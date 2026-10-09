<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('admin_settings', 'sslcommerz_status')) {
            Schema::table('admin_settings', function (Blueprint $table): void {
                $table->boolean('sslcommerz_status')->default(false)->after('sslcommerz_mode');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('admin_settings', 'sslcommerz_status')) {
            Schema::table('admin_settings', function (Blueprint $table): void {
                $table->dropColumn('sslcommerz_status');
            });
        }
    }
};
