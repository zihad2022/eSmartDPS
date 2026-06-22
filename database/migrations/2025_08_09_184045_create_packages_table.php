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
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('discount_value')->nullable()->default(0);
            $table->unsignedBigInteger('discount_type')->nullable();
            $table->unsignedBigInteger('billing_cycle')->nullable();
            $table->integer('member_limit')->default(0);
            $table->integer('user_limit')->nullable();
            $table->integer('project_limit')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_trial')->default(false);
            $table->integer('trial_days')->default(0);
            $table->json('features')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
