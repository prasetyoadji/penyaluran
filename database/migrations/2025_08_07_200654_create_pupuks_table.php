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
        Schema::create('pupuks', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 45);
            $table->foreignId('jenis_id')->constrained('jenis')->cascadeOnDelete();
            $table->foreignId('satuan_id')->constrained('satuans')->cascadeOnDelete();
            $table->foreignId('perusahaan_id')->constrained('perusahaans')->cascadeOnDelete();
            $table->unsignedMediumInteger('stok')->default(0);
            $table->text('deskripsi');
            $table->unsignedBigInteger('harga');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pupuks');
    }
};
