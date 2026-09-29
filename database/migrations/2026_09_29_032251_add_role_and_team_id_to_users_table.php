<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // pelapor: civitas kampus yang membuat tiket (default).
            // teknisi: anggota tim yang menangani tiket.
            // supervisor: atasan tim, menerima eskalasi.
            // admin: mengelola master data & lihat semua laporan.
            $table->string('role')->default('pelapor')->after('email');
            // Tim tempat seorang teknisi bertugas. Null untuk pelapor/admin.
            $table->foreignId('team_id')->nullable()->after('role')->constrained('teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('team_id');
            $table->dropColumn('role');
        });
    }
};
