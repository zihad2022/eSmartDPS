<?php

use App\Enums\Package\BillingCycle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedBigInteger('discount_value')->default(0);
            $table->unsignedTinyInteger('discount_type')->nullable();
            $table->unsignedTinyInteger('billing_cycle')->default(BillingCycle::MONTHLY->value);
            $table->unsignedInteger('member_limit')->default(0);
            $table->unsignedInteger('user_limit')->default(0);
            $table->unsignedInteger('project_limit')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_trial')->default(false);
            $table->unsignedSmallInteger('trial_days')->default(0);
            $table->json('features')->nullable();
            $table->timestamps();

            $table->index(['is_active', 'billing_cycle']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
