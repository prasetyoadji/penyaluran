<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pengajuans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembeli_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users')->cascadeOnDelete();
            $table->date('tanggal_persetujuan')->nullable();
            $table->string('status', 15)->default('menunggu');
            $table->text('alasan_pengajuan');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuans');
    }
};