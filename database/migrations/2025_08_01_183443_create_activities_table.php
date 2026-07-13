<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table): void {
            $table->id();
            $table->nullableMorphs('causer');
            $table->string('activity');
            $table->string('ip_address', 45)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('version', 50)->nullable();
            $table->string('system', 100)->nullable();
            $table->timestamp('activity_date')->nullable();
            $table->timestamps();

            $table->index(['activity_date', 'activity']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
