<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('escalations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->cascadeOnDelete();
            $table->timestamp('escalated_at');
            // Supervisor tim tiket tersebut. Fallback ke admin jika tim/supervisor tidak ada
            // (lihat App\Console\Commands\EscalateOverdueTickets), supaya eskalasi tetap tercatat
            // dan bisa ditindaklanjuti walaupun tim belum punya atasan.
            $table->foreignId('escalated_to')->constrained('users')->cascadeOnDelete();
            $table->string('reason');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('escalations');
    }
};
