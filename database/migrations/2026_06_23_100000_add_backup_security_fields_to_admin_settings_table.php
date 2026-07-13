<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin_settings', function (Blueprint $table): void {
            $table->boolean('backup_enabled')->default(false)->after('email_message_template');
            $table->string('backup_frequency', 20)->default('daily')->after('backup_enabled');
            $table->unsignedSmallInteger('session_timeout_minutes')->default(30)->after('backup_frequency');
            $table->timestamp('last_backup_at')->nullable()->after('session_timeout_minutes');
            $table->string('last_backup_path')->nullable()->after('last_backup_at');
        });
    }

    public function down(): void
    {
        Schema::table('admin_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'backup_enabled',
                'backup_frequency',
                'session_timeout_minutes',
                'last_backup_at',
                'last_backup_path',
            ]);
        });
    }
};
