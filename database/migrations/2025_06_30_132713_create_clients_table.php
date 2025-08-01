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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            // Parent client (for sub-user management)
            $table->foreignId('parent_id')->nullable()->constrained('clients')->onDelete('cascade');

            // Authentication
            $table->string('user_id')->unique();
            $table->string('password');
            $table->rememberToken();

            // Personal Information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('profile_photo')->nullable();

            // Contact Information
            $table->string('email')->unique();
            $table->string('phone')->nullable();

            // NID Information
            $table->string('nid_number')->nullable();
            $table->string('nid_card_front')->nullable();
            $table->string('nid_card_back')->nullable();

            // Location
            $table->string('division')->nullable();
            $table->string('district')->nullable();
            $table->string('address')->nullable();
            $table->string('postal_code')->nullable();

            // Role & Permissions
            $table->enum('role', ['admin', 'manager', 'editor'])->default('manager');

            // Account Status
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
