<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_settings', function (Blueprint $table): void {
            $table->text('bkash_id_token')->nullable()->after('bkash_status');
            $table->timestamp('bkash_token_expires_at')->nullable()->after('bkash_id_token');
        });
    }

    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'bkash_id_token',
                'bkash_token_expires_at',
            ]);
        });
    }
};
