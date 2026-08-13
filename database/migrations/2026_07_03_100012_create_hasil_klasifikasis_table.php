<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel hasil klasifikasi Naive Bayes + WOA.
     */
    public function up(): void
    {
        Schema::create('hasil_klasifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_bos_id')->constrained('periode_bos')->cascadeOnDelete();
            $table->foreignId('bku_item_id')->nullable()->constrained('bku_items')->nullOnDelete();
            $table->foreignId('rkas_item_id')->nullable()->constrained('rkas_items')->nullOnDelete();
            $table->string('kode_rekening');
            // Fitur X1-X8
            $table->string('x1_kode_rekening');
            $table->decimal('x2_standar_komponen_s', 15, 2)->default(0);
            $table->decimal('x3_standar_komponen_k', 15, 2)->default(0);
            $table->decimal('x4_jumlah_anggaran', 15, 2)->default(0);
            $table->decimal('x5_pengeluaran', 15, 2)->default(0);
            $table->decimal('x6_selisih', 15, 2)->default(0);
            $table->decimal('x7_persentase_realisasi', 8, 2)->default(0);
            $table->tinyInteger('x8_status_kesesuaian')->default(0);
            // Hasil klasifikasi
            $table->enum('label_prediksi', ['Wajar', 'Menyimpang']);
            $table->decimal('probabilitas_wajar', 10, 6)->default(0);
            $table->decimal('probabilitas_menyimpang', 10, 6)->default(0);
            $table->timestamp('classified_at')->nullable();
            $table->foreignId('classified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_klasifikasis');
    }
};
