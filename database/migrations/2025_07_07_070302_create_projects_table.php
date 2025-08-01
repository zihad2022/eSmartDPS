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

            $table->decimal('investment_amount', 12, 2)->default(0);
            $table->decimal('expected_return', 5, 2)->default(0);

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('duration')->nullable();

            $table->text('description')->nullable();

            $table->unsignedTinyInteger('status')->default(0);

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
