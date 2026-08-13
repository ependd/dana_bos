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
        // Add barang datang tracking columns to rkas_items
        Schema::table('rkas_items', function (Blueprint $table) {
            $table->string('status_barang_datang')->default('belum_datang')->after('persentase_perubahan'); // belum_datang, diterima, ditolak
            $table->timestamp('tanggal_barang_datang')->nullable()->after('status_barang_datang');
            $table->text('catatan_barang_datang')->nullable()->after('tanggal_barang_datang');
        });

        // Add foto_barang column to bku_items
        Schema::table('bku_items', function (Blueprint $table) {
            $table->string('foto_barang')->nullable()->after('saldo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rkas_items', function (Blueprint $table) {
            $table->dropColumn(['status_barang_datang', 'tanggal_barang_datang', 'catatan_barang_datang']);
        });

        Schema::table('bku_items', function (Blueprint $table) {
            $table->dropColumn(['foto_barang']);
        });
    }
};
