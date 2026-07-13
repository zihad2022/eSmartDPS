<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin_settings', function (Blueprint $table): void {
            $table->id();

            $table->string('site_name')->nullable();
            $table->string('site_slogan')->nullable();
            $table->text('site_description')->nullable();
            $table->text('site_keywords')->nullable();
            $table->text('meta_codes')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('graph_thumbnail')->nullable();

            $table->string('helpline_number', 30)->nullable();
            $table->string('email_address')->nullable();
            $table->text('office_address')->nullable();
            $table->text('google_map')->nullable();

            // Names match the controllers and model exactly.
            $table->string('facebook_page')->nullable();
            $table->string('facebook_group')->nullable();
            $table->string('whatsapp_channel')->nullable();
            $table->string('telegram_channel')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter_x')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();

            $table->string('currency', 10)->default('BDT');
            $table->unsignedBigInteger('late_fee')->nullable();

            $table->string('bkash_base_url')->nullable();
            $table->string('bkash_username')->nullable();
            $table->text('bkash_password')->nullable();
            $table->text('bkash_app_key')->nullable();
            $table->text('bkash_app_secret')->nullable();
            $table->decimal('bkash_charge', 10, 2)->default(0);
            $table->boolean('bkash_status')->default(false);

            $table->string('sslcommerz_store_id')->nullable();
            $table->text('sslcommerz_store_password')->nullable();
            $table->string('sslcommerz_mode', 20)->nullable();

            $table->text('sms_api_key')->nullable();
            $table->string('sms_client_id')->nullable();
            $table->string('sms_sender_id')->nullable();
            $table->string('sms_api_url')->nullable();
            $table->string('sms_balance_api')->nullable();
            $table->text('sms_message_template')->nullable();
            $table->boolean('sms_status')->default(false);

            $table->string('mail_host')->nullable();
            $table->unsignedSmallInteger('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->text('mail_password')->nullable();
            $table->string('mail_encryption', 20)->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->text('email_message_template')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
