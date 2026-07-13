<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('ledger_category_id')
                ->constrained('ledger_categories')
                ->restrictOnDelete();
            $table->unsignedTinyInteger('type');
            $table->string('description');
            $table->unsignedBigInteger('amount');
            $table->date('entry_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'entry_date']);
            $table->index(['client_id', 'type', 'entry_date']);
            $table->index(['ledger_category_id', 'entry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledgers');
    }
};
