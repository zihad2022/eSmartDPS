<?php

use App\Enums\InvoiceStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();

            // Immutable package snapshot for historical invoices.
            $table->string('package_name');
            $table->text('package_description')->nullable();
            $table->date('billing_start');
            $table->date('billing_end');
            $table->date('due_date')->nullable();
            $table->string('invoice_number', 50)->unique();
            $table->unsignedBigInteger('invoice_amount');
            $table->unsignedTinyInteger('status')->default(InvoiceStatus::UNPAID->value);
            $table->timestamp('paid_at')->nullable();

            $table->string('payment_reference', 100)->nullable();
            $table->string('payment_id', 100)->nullable();
            $table->string('trx_id', 100)->nullable();
            $table->unsignedTinyInteger('payment_method')->nullable();
            $table->string('wallet_address')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->unique(
                ['client_id', 'package_id', 'billing_start', 'billing_end'],
                'invoices_client_package_period_unique'
            );
            $table->index(['client_id', 'due_date']);
            $table->index(['status', 'created_at']);
            $table->index('payment_reference');
            $table->index('payment_id');
            $table->index('trx_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
