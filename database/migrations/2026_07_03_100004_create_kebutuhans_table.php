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
        Schema::create('kebutuhans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('periode_bos_id')->constrained('periode_bos')->cascadeOnDelete();
            $table->foreignId('katalog_barang_id')->constrained('katalog_barangs')->cascadeOnDelete();
            $table->string('kategori_belanja')->nullable();
            $table->integer('volume');
            $table->string('satuan');
            $table->text('keterangan')->nullable();
            $table->enum('status', ['diajukan', 'diterima', 'ditolak', 'tersedia', 'dikonfirmasi'])->default('diajukan');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('tanggal_konfirmasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kebutuhans');
    }
};
