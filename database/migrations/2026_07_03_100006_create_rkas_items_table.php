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
        Schema::create('rkas_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rkas_id')->constrained('rkas')->cascadeOnDelete();
            $table->foreignId('kebutuhan_id')->nullable()->constrained('kebutuhans')->nullOnDelete();
            $table->string('kode_rekening');
            $table->string('uraian_belanja');
            // Nilai sebelum perubahan (untuk tracking revisi)
            $table->decimal('volume_sebelum', 15, 2)->nullable();
            $table->string('satuan_sebelum')->nullable();
            $table->decimal('harga_satuan_sebelum', 15, 2)->nullable();
            $table->decimal('jumlah_sebelum', 15, 2)->nullable();
            $table->decimal('standar_komponen_s_sebelum', 15, 2)->nullable();
            $table->decimal('standar_komponen_k_sebelum', 15, 2)->nullable();
            // Nilai setelah perubahan (nilai aktual/current)
            $table->decimal('volume_setelah', 15, 2)->default(0);
            $table->string('satuan_setelah');
            $table->decimal('harga_satuan_setelah', 15, 2)->default(0);
            $table->decimal('jumlah_setelah', 15, 2)->default(0);
            $table->decimal('standar_komponen_s_setelah', 15, 2)->default(0);
            $table->decimal('standar_komponen_k_setelah', 15, 2)->default(0);
            // Perubahan
            $table->decimal('bertambah_berkurang', 15, 2)->default(0);
            $table->decimal('persentase_perubahan', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rkas_items');
    }
};
