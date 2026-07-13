<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('username', 100)->unique();
            $table->string('email')->unique();
            $table->string('phone', 30)->unique();
            $table->string('password');
            $table->string('profile_photo')->nullable();
            $table->boolean('status')->default(true)->index();
            $table->timestamp('last_login')->nullable()->index();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
