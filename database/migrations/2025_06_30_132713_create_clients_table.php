<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('clients')
                ->cascadeOnDelete();

            $table->string('user_id', 50)->unique();
            $table->string('password');
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('profile_photo')->nullable();

            $table->string('email')->unique();
            $table->string('phone', 30)->nullable()->unique();

            $table->string('nid_number', 100)->nullable()->index();
            $table->string('nid_card_front')->nullable();
            $table->string('nid_card_back')->nullable();

            $table->string('division', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->text('address')->nullable();
            $table->string('postal_code', 20)->nullable();

            // String instead of database enum so roles can evolve without a schema migration.
            $table->string('role', 50)->default('manager')->index();
            $table->boolean('status')->default(true)->index();
            $table->timestamps();

            $table->index(['parent_id', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
