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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('project_category_id')->constrained()->onDelete('cascade');

            $table->string('name');
            $table->string('slug')->unique();

            $table->unsignedBigInteger('investment_amount')->default(0);
            $table->unsignedBigInteger('expected_return')->default(0);
            $table->string('expected_return_type')->default('percentage');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('duration')->nullable();

            $table->text('description')->nullable();

            $table->unsignedBigInteger('status')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
