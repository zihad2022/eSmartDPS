<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_settings', function (Blueprint $table) {
            $table->id();
            
            // Belongs to Client
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
        
            // -----------------------
            // General Settings
            // -----------------------
            $table->string('organization_name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('address')->nullable();
            $table->string('currency')->default('BDT');
        
            // -----------------------
            // Share Settings
            // -----------------------
            $table->decimal('share_price', 10, 2)->nullable();
            $table->integer('minimum_shares')->nullable();
            $table->integer('maximum_shares')->nullable();
            $table->decimal('share_transfer_fee', 10, 2)->nullable();
            $table->boolean('allow_partial_shares')->default(false);
        
            // -----------------------
            // Payment Settings
            // -----------------------
            $table->string('payment_due_date')->nullable(); // e.g., '1', '15', '30'
            $table->decimal('late_payment_fee', 10, 2)->nullable();
            $table->integer('grace_period_days')->nullable();
            $table->json('payment_methods')->nullable(); // store multiple methods as JSON
        
            // -----------------------
            // Notification Settings
            // -----------------------
            $table->string('sms_api_provider')->nullable();
            $table->string('sms_api_key')->nullable();
            $table->boolean('email_payments')->default(false);
            $table->boolean('email_reminders')->default(false);
            $table->boolean('email_reports')->default(false);
            $table->boolean('sms_payments')->default(false);
            $table->boolean('sms_reminders')->default(false);
        
            // -----------------------
            // Backup & Security
            // -----------------------
            $table->boolean('auto_backup')->default(false);
            $table->boolean('two_factor_auth')->default(false);
            $table->boolean('session_timeout')->default(true);
            $table->boolean('login_notifications')->default(true);
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_settings');
    }
};
