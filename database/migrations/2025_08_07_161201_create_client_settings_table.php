<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->unique()->constrained()->cascadeOnDelete();

            $table->string('organization_name')->nullable();
            $table->string('short_name', 100)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->text('address')->nullable();
            $table->string('currency', 10)->default('BDT');

            $table->unsignedBigInteger('share_price')->nullable();
            $table->unsignedInteger('minimum_shares')->default(1);
            $table->unsignedInteger('maximum_shares')->default(1000);
            $table->unsignedBigInteger('share_transfer_fee')->nullable();
            $table->boolean('allow_partial_shares')->default(false);

            $table->unsignedTinyInteger('payment_due_date')->nullable();
            $table->unsignedBigInteger('late_payment_fee')->nullable();
            $table->unsignedSmallInteger('grace_period_days')->nullable();
            $table->json('payment_methods')->nullable();

            $table->string('sms_api_provider')->nullable();
            $table->text('sms_api_key')->nullable();
            $table->boolean('email_payment_confirmations')->default(false);
            $table->boolean('email_payment_reminders')->default(false);
            $table->boolean('email_payment_reports')->default(false);
            $table->boolean('sms_payment_confirmations')->default(false);
            $table->boolean('sms_payment_reminders')->default(false);

            $table->boolean('auto_backup')->default(false);
            $table->boolean('two_factor_auth')->default(false);
            $table->boolean('session_timeout')->default(true);
            $table->boolean('login_notifications')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_settings');
    }
};
