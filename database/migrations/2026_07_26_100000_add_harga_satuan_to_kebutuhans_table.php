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
            $table->decimal('harga_satuan', 15, 2)->nullable()->after('katalog_barang_id');
        });

        // Populate harga_satuan for existing records from katalog_barangs
        DB::statement("
            UPDATE kebutuhans k
            JOIN katalog_barangs kb ON k.katalog_barang_id = kb.id
            SET k.harga_satuan = kb.harga_referensi
            WHERE k.harga_satuan IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kebutuhans', function (Blueprint $table) {
            $table->dropColumn('harga_satuan');
        });
    }
};
