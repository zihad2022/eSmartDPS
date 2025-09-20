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
        Schema::create('otp_codes', function (Blueprint $table) {
            $table->id();
        
            // Polymorphic relation (userable_id, userable_type)
            $table->morphs('userable');
        
            $table->string('phone')->nullable();
            $table->string('otp');
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_used')->default(false);
        
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('otp_codes');
    }
};
