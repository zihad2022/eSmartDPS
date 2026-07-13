<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->string('payment_id', 30)->unique();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('amount');
            $table->unsignedTinyInteger('payment_method')->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->string('reference_number', 100)->nullable();
            $table->string('status', 20)->default(PaymentStatus::DUE->value);
            $table->dateTime('paid_at')->nullable();
            $table->date('due_date')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['member_id', 'status']);
            $table->index(['client_id', 'member_id']);
            $table->index(['client_id', 'due_date']);
            $table->index(['client_id', 'paid_at']);
            $table->index('payment_method');
            $table->index('transaction_id');
            $table->index('reference_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
