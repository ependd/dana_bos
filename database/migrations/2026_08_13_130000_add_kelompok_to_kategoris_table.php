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
        Schema::table('kategoris', function (Blueprint $table) {
            $table->string('kelompok')->default('Lainnya')->after('nama');
        });

        // Update default categories with appropriate kelompok
        DB::table('kategoris')->where('nama', 'LIKE', '%ATK%')->update(['kelompok' => 'ATK & Operasional']);
        DB::table('kategoris')->where('nama', 'LIKE', '%Buku%')->orWhere('nama', 'LIKE', '%Bahan Ajar%')->update(['kelompok' => 'Buku']);
        DB::table('kategoris')->where('nama', 'LIKE', '%Lab%')->orWhere('nama', 'LIKE', '%TIK%')->orWhere('nama', 'LIKE', '%Mebeler%')->orWhere('nama', 'LIKE', '%Peralatan%')->update(['kelompok' => 'Sapras']);
        DB::table('kategoris')->where('nama', 'LIKE', '%Honor%')->update(['kelompok' => 'Honorarium & Jasa']);
        DB::table('kategoris')->where('nama', 'LIKE', '%Pemeliharaan%')->update(['kelompok' => 'Pemeliharaan & Perbaikan']);
        DB::table('kategoris')->where('nama', 'LIKE', '%Kegiatan%')->update(['kelompok' => 'ATK & Operasional']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kategoris', function (Blueprint $table) {
            $table->dropColumn('kelompok');
        });
    }
};
