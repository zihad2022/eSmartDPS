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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            /**
             * Client relation
             * If a client is deleted, all related invoices will also be deleted.
             */
            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('cascade');
            $table->foreignId('package_id')
                ->constrained('packages')
                ->onDelete('cascade');

            /**
             * Package details
             */
            $table->string('package_name');          // Name of the purchased package
            $table->string('package_description');   // Short description of the package

            $table->string('payment_reference')->nullable();
            /**
             * Invoice details
             */
            $table->string('invoice_number')->unique();  // Unique invoice number for tracking
            $table->unsignedBigInteger('invoice_amount'); // Total amount of the invoice in cents (integer)
            $table->integer('status')->default(1);      // Invoice status (Enum: unpaid, paid, refunded, cancelled)
            $table->timestamp('paid_at')->nullable();   // Timestamp of when the invoice was paid
            $table->timestamp('due_date');              // Invoice due date

            /**
             * Payment gateway details
             */
            $table->string('payment_id')->nullable();      // Payment gateway ID (if available)
            $table->string('trx_id')->nullable();          // Transaction ID from payment gateway
            $table->string('payment_method')->nullable();  // Payment method (e.g., card, PayPal, crypto)
            $table->string('wallet_address')->nullable();  // Wallet address for crypto or other payments

            /**
             * Timestamps
             */
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
