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

            // 🔗 Relation
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');

            // 🧾 Invoice Details
            $table->string('invoice_number')->unique();
            $table->decimal('invoice_amount', 10, 2);

            // 📌 Status (Use Enum for clarity: unpaid, paid, refunded, cancelled)
            $table->integer('status')->default(1);

            // 💳 Payment Details
            $table->string('payment_id')->nullable();
            $table->string('trx_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('wallet_address')->nullable();

            // 🕒 Timestamps
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
