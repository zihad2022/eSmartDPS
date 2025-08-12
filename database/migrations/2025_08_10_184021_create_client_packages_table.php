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
        Schema::create('client_packages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');

            $table->dateTime('starts_at');
            $table->dateTime('ends_at');

            $table->boolean('is_trial')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_packages');
    }
};
