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
        Schema::create('pakages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('discount_value', 10, 2)->default(0);
            $table->enum('discount_type', ['percentage', 'amount'])->default('percentage');
            $table->enum('billing_cycle', ['monthly', 'yearly'])->default('monthly');
            $table->integer('member_limit')->default(0);
            $table->integer('user_limit')->default(0);
            $table->integer('project_limit')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('has_trial')->default(false);
            $table->integer('trial_days')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pakages');
    }
};
