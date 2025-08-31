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
    
            // Relation with clients table (if a client is deleted, all related invoices will also be deleted)
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
    
            // Package details
            $table->string('package_name');          // Name of the purchased package
            $table->string('package_description');   // Short description of the package
    
            // Invoice details
            $table->string('invoice_number')->unique();  // Unique invoice number for tracking
            $table->unsignedInteger('invoice_amount');   // Total amount of the invoice (stored as integer for safety)
    
            // Invoice status (casted as Enum in the model: unpaid, paid, refunded, cancelled, etc.)
            $table->integer('status')->default(1);
    
            // Payment details
            $table->string('payment_id')->nullable();      // Payment gateway ID (if available)
            $table->string('trx_id')->nullable();          // Transaction ID from payment gateway
            $table->string('payment_method')->nullable();  // Payment method (e.g., card, PayPal, crypto, etc.)
            $table->string('wallet_address')->nullable();  // Wallet address for crypto or other payments
    
            // Laravel timestamps (created_at, updated_at)
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
