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
        Schema::create('ticket_replies', function (Blueprint $table) {
            $table->id();

            // 🔗 Ticket relation
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');

            // 👤 Reply sender (client or admin)
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('admins')->onDelete('cascade');

            // 📝 Message content
            $table->text('message');

            // 📎 Optional file
            $table->string('attachment')->nullable();

            // 📅 Timestamps
            $table->timestamps();

            // ✅ Optional index for faster querying by ticket
            $table->index('ticket_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_reaplies');
    }
};
