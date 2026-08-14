<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kebutuhans', function (Blueprint $table) {
            $table->string('nama_barang')->nullable()->after('katalog_barang_id');
        });

        // Backfill nama_barang for existing records from katalog_barangs
        try {
            DB::statement("
                UPDATE kebutuhans k
                JOIN katalog_barangs kb ON k.katalog_barang_id = kb.id
                SET k.nama_barang = kb.nama_barang
                WHERE k.nama_barang IS NULL
            ");
        } catch (\Throwable $e) {
            // Ignore if no records
        }

        // Change katalog_barang_id foreign key constraint to nullable and nullOnDelete
        Schema::table('kebutuhans', function (Blueprint $table) {
            $table->dropForeign(['katalog_barang_id']);
            $table->unsignedBigInteger('katalog_barang_id')->nullable()->change();
            $table->foreign('katalog_barang_id')->references('id')->on('katalog_barangs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kebutuhans', function (Blueprint $table) {
            $table->dropForeign(['katalog_barang_id']);
            $table->dropColumn('nama_barang');
            $table->unsignedBigInteger('katalog_barang_id')->change();
            $table->foreign('katalog_barang_id')->references('id')->on('katalog_barangs')->cascadeOnDelete();
        });
    }
};
