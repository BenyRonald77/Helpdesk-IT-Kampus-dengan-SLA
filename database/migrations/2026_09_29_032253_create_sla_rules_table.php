<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sla_rules', function (Blueprint $table) {
            $table->id();
            $table->string('priority')->unique(); // low, medium, high, critical
            // Target waktu respons pertama (menit sejak tiket dibuat).
            $table->unsignedInteger('response_minutes');
            // Target waktu penyelesaian penuh (menit sejak tiket dibuat). Ini yang menentukan sla_due_at tiket.
            $table->unsignedInteger('resolution_minutes');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sla_rules');
    }
};
