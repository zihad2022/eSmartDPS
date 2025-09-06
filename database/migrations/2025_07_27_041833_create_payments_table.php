<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\PaymentStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
        
            $table->string('payment_id')->unique(); // Unique payment code/ID
        
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Linked client
            $table->foreignId('member_id')->constrained()->onDelete('cascade'); // Linked member/customer
        
            $table->decimal('amount', 12, 2); // Payment amount
            $table->string('currency', 10)->default('USD'); // Currency (default USD)
        
            $table->integer('payment_method')->nullable(); // e.g. cash, card, bank
            $table->string('transaction_id')->nullable(); // Gateway transaction ID
            $table->string('reference')->nullable(); // Invoice/order ref
        
            $table->string('status'); // Payment status
        
            $table->dateTime('paid_at')->nullable(); // When paid
            $table->dateTime('due_date')->nullable(); // When due
        
            $table->json('meta')->nullable(); // Extra data/notes
        
            $table->timestamps(); // created_at, updated_at
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
