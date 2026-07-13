<?php

use App\Enums\ProjectStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_category_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name');
            $table->string('slug');
            $table->unsignedBigInteger('investment_amount')->default(0);
            $table->unsignedBigInteger('expected_return')->default(0);
            $table->string('expected_return_type', 20)->default('percent');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('status')->default(ProjectStatus::ACTIVE->value);
            $table->timestamps();

            $table->unique(['client_id', 'slug']);
            $table->index(['client_id', 'status']);
            $table->index(['client_id', 'project_category_id']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
