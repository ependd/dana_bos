<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel untuk import data historis RKAS (2022-2025) sebagai data pelatihan.
     */
    public function up(): void
    {
        Schema::create('data_training_rkas', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('kode_rekening');
            $table->string('uraian_belanja')->nullable();
            $table->decimal('volume_sebelum', 15, 2)->nullable();
            $table->string('satuan_sebelum')->nullable();
            $table->decimal('harga_satuan_sebelum', 15, 2)->nullable();
            $table->decimal('jumlah_sebelum', 15, 2)->nullable();
            $table->decimal('standar_komponen_s_sebelum', 15, 2)->nullable();
            $table->decimal('standar_komponen_k_sebelum', 15, 2)->nullable();
            $table->decimal('volume_setelah', 15, 2)->nullable();
            $table->string('satuan_setelah')->nullable();
            $table->decimal('harga_satuan_setelah', 15, 2)->nullable();
            $table->decimal('jumlah_setelah', 15, 2)->nullable();
            $table->decimal('standar_komponen_s_setelah', 15, 2)->nullable();
            $table->decimal('standar_komponen_k_setelah', 15, 2)->nullable();
            $table->decimal('bertambah_berkurang', 15, 2)->nullable();
            $table->decimal('persentase_perubahan', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_training_rkas');
    }
};
