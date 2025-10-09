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
            $table->string('payment_id', 20)->unique();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('amount');
            $table->unsignedTinyInteger('payment_method')->nullable()->index();
            $table->string('reference_number', 64)->nullable()->index();
            $table->string('status', 20)->default(PaymentStatus::DUE->value)->index();
            $table->dateTime('paid_at')->nullable()->index();
            $table->dateTime('due_date')->nullable()->index();
            $table->json('meta')->nullable();
            $table->timestamps();

            // Helpful composite indexes for common filters
            $table->index(['member_id', 'status']);
            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'member_id']);
            $table->index('created_at');
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
