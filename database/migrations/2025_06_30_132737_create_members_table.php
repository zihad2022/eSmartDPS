<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('member_id', 50)->unique();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('password');
            $table->boolean('status')->default(true);
            $table->string('profile_photo')->nullable();
            $table->unsignedInteger('share_quantity')->default(0);
            $table->unsignedBigInteger('total_balance')->default(0);
            $table->rememberToken();
            $table->timestamps();

            $table->unique(['client_id', 'email']);
            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'phone']);
            $table->index(['client_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
