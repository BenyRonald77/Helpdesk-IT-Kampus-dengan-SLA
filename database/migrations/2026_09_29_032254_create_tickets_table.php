<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            // Tim yang menangani. Diisi saat tiket ditugaskan (manual atau otomatis dari kategori).
            $table->foreignId('team_id')->nullable()->constrained('teams')->nullOnDelete();
            // Teknisi yang ditugaskan menangani tiket ini.
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('priority'); // low, medium, high, critical
            $table->string('status')->default('open'); // open, in_progress, escalated, resolved, closed
            // Diisi saat teknisi pertama kali ditugaskan / merespons tiket.
            $table->timestamp('first_responded_at')->nullable();
            // Diisi saat tiket ditandai selesai oleh teknisi.
            $table->timestamp('resolved_at')->nullable();
            // Target waktu penyelesaian, dihitung & disimpan saat tiket dibuat atau saat prioritas berubah:
            // sla_due_at = created_at + resolution_minutes (dari sla_rules sesuai priority saat itu).
            // Disimpan (bukan dihitung on-the-fly) supaya riwayat tetap stabil walau aturan SLA berubah di kemudian hari.
            $table->timestamp('sla_due_at')->nullable();
            // true jika tiket pernah melewati sla_due_at, baik terdeteksi oleh job eskalasi maupun saat resolve terlambat.
            $table->boolean('sla_breached')->default(false);
            $table->timestamps();

            $table->index(['status', 'sla_due_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
