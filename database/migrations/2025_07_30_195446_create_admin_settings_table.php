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
        Schema::create('admin_settings', function (Blueprint $table) {
            $table->id();

            /* ========== General Settings ========== */
            $table->string('site_name')->nullable();
            $table->string('site_slogan')->nullable();
            $table->text('site_description')->nullable();
            $table->text('site_keywords')->nullable();
            $table->text('meta_codes')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('graph_thumbnail')->nullable();

            /* ========== Contact Info ========== */
            $table->string('helpline_number')->nullable();
            $table->string('email_address')->nullable();
            $table->string('office_address')->nullable();
            $table->text('google_map')->nullable();

            /* ========== Social Media ========== */
            $table->string('facebook_page')->nullable();
            $table->string('facebook_group')->nullable();
            $table->string('whatsapp_channel')->nullable();
            $table->string('telegram_channel')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('twitter_x')->nullable();
            $table->string('youtube')->nullable();
            $table->string('tiktok')->nullable();

            /* ========== Payment Settings ========== */
            $table->string('currency')->nullable();
            $table->decimal('late_fee', 10, 2)->nullable();

            // bKash Payment
            $table->string('bkash_app_key')->nullable();
            $table->string('bkash_app_secret')->nullable();
            $table->string('bkash_username')->nullable();
            $table->string('bkash_password')->nullable();

            // UddoktaPay
            $table->string('uddoktapay_api_key')->nullable();
            $table->string('uddoktapay_secret')->nullable();
            $table->string('uddoktapay_callback_url')->nullable();

            // SSLCommerz
            $table->string('sslcommerz_store_id')->nullable();
            $table->string('sslcommerz_store_password')->nullable();
            $table->string('sslcommerz_mode')->nullable(); // live or sandbox

            /* ========== SMS Settings ========== */
            $table->string('sms_api_key')->nullable();
            $table->string('sms_secret_key')->nullable();
            $table->string('sms_sender_id')->nullable();
            $table->string('sms_api_url')->nullable();
            $table->string('sms_balance_api')->nullable();
            $table->text('sms_message_template')->nullable();

            /* ========== Email Settings ========== */
            $table->string('mail_host')->nullable();
            $table->string('mail_port')->nullable();
            $table->string('mail_username')->nullable();
            $table->string('mail_password')->nullable();
            $table->string('mail_encryption')->nullable();
            $table->string('mail_from_address')->nullable();
            $table->string('mail_from_name')->nullable();
            $table->text('email_message_template')->nullable();

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_settings');
    }
};
