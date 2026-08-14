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
        Schema::create('bkus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rkas_id')->constrained('rkas')->cascadeOnDelete();
            $table->foreignId('periode_bos_id')->constrained('periode_bos')->cascadeOnDelete();
            $table->string('kode_bku')->unique();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->decimal('total_realisasi', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bkus');
    }
};
