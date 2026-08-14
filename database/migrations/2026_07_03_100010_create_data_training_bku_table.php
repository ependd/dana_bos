<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tabel untuk import data historis BKU (2022-2025) dengan label manual.
     */
    public function up(): void
    {
        Schema::create('data_training_bku', function (Blueprint $table) {
            $table->id();
            $table->string('tahun');
            $table->integer('nomor_urut')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('nomor_bukti')->nullable();
            $table->string('uraian')->nullable();
            $table->decimal('nilai_rincian', 15, 2)->nullable();
            $table->string('kode_rekening');
            $table->decimal('penerimaan', 15, 2)->default(0);
            $table->decimal('pengeluaran', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->enum('label_aktual', ['Wajar', 'Menyimpang']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_training_bku');
    }
};
