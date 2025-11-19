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

            $table->foreignId('client_id')
                ->constrained('clients')
                ->onDelete('cascade');

            $table->foreignId('package_id')
                ->constrained('packages')
                ->onDelete('cascade');

            /**
             * Package details snapshot
             */
            $table->string('package_name');
            $table->string('package_description');

            /**
             * Billing period
             */
            $table->date('billing_start');    // <-- IMPORTANT
            $table->date('billing_end');      // <-- IMPORTANT

            /**
             * Invoice details
             */
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('invoice_amount');
            $table->integer('status')->default(1);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('next_invoice_at');

            /**
             * Payment info
             */
            $table->string('payment_reference')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('trx_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('wallet_address')->nullable();

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
