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
        Schema::create('ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('category_id')
                ->constrained('ledger_categories')
                ->cascadeOnDelete();

            $table->foreignId('client_id')
                ->nullable()
                ->constrained('clients')
                ->cascadeOnDelete(); // If you want ledger per client

            $table->string('title'); // Short title for the entry
            $table->text('description')->nullable(); // Optional details

            $table->decimal('amount', 12, 2); // Amount of entry
            $table->enum('type', ['income', 'expense']); // Redundant but useful for filtering quickly

            $table->date('entry_date'); // Date of transaction
            $table->string('reference_no')->nullable(); // Invoice or reference number
            $table->string('payment_method')->nullable(); // Cash, Bank, Card, etc.

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
