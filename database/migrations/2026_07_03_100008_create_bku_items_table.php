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
        Schema::create('bku_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bku_id')->constrained('bkus')->cascadeOnDelete();
            $table->foreignId('rkas_item_id')->nullable()->constrained('rkas_items')->nullOnDelete();
            $table->integer('nomor_urut');
            $table->date('tanggal');
            $table->string('nomor_bukti')->nullable();
            $table->string('uraian');
            $table->decimal('nilai_rincian', 15, 2)->default(0);
            $table->string('kode_rekening');
            $table->decimal('penerimaan', 15, 2)->default(0);
            $table->decimal('pengeluaran', 15, 2)->default(0);
            $table->decimal('saldo', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bku_items');
    }
};
