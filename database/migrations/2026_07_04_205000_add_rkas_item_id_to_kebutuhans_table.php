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
        Schema::table('kebutuhans', function (Blueprint $table) {
            $table->foreignId('rkas_item_id')->nullable()->after('katalog_barang_id')->constrained('rkas_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kebutuhans', function (Blueprint $table) {
            $table->dropForeign(['rkas_item_id']);
            $table->dropColumn('rkas_item_id');
        });
    }
};
