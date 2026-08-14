<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel fitur X1-X8 hasil join & feature engineering dari data training RKAS+BKU.
     */
    public function up(): void
    {
        Schema::create('data_trainings', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->string('kode_rekening');
            // X1: Kode Rekening (kategorikal, join key)
            $table->string('x1_kode_rekening');
            // X2: Standar Komponen S Setelah Perubahan (numerik)
            $table->decimal('x2_standar_komponen_s', 15, 2)->default(0);
            // X3: Standar Komponen K Setelah Perubahan (numerik)
            $table->decimal('x3_standar_komponen_k', 15, 2)->default(0);
            // X4: Jumlah Anggaran Setelah Perubahan (numerik)
            $table->decimal('x4_jumlah_anggaran', 15, 2)->default(0);
            // X5: Pengeluaran BKU (numerik) — dieliminasi WOA tapi disimpan
            $table->decimal('x5_pengeluaran', 15, 2)->default(0);
            // X6: Selisih Realisasi vs Anggaran (numerik) — dieliminasi WOA tapi disimpan
            $table->decimal('x6_selisih', 15, 2)->default(0);
            // X7: Persentase Realisasi (numerik)
            $table->decimal('x7_persentase_realisasi', 8, 2)->default(0);
            // X8: Status Kesesuaian Kode Rekening (0/1)
            $table->tinyInteger('x8_status_kesesuaian')->default(0);
            // Label
            $table->enum('label', ['Wajar', 'Menyimpang']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_trainings');
    }
};
