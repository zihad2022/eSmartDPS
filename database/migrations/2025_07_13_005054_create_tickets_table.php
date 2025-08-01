<?php

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            // 👤 Ownership
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); // Main client account

            // 📝 Ticket Info
            $table->string('ticket_number')->unique(); // Example: TKT-1001
            $table->string('subject');
            $table->text('message');

            // 📌 Status & Priority
            $table->integer('status')->default(TicketStatus::OPEN->value); // Use Enum or constants
            $table->integer('priority')->default(TicketPriority::MEDIUM->value);

            // 🛠 Admin Interaction
            $table->text('admin_notes')->nullable();

            // 🕒 Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
