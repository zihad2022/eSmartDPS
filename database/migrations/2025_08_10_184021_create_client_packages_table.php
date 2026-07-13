<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('packages')->restrictOnDelete();
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->boolean('is_trial')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status', 20)->default('active');
            $table->timestamps();

            $table->index(['client_id', 'is_active', 'status', 'ends_at'], 'client_subscription_lookup');
            $table->index(['client_id', 'package_id', 'is_trial']);
            $table->index(['package_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_packages');
    }
};
