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
        Schema::table('hasil_klasifikasis', function (Blueprint $table) {
            $table->string('label_prediksi', 50)->change();
        });

        Schema::table('data_trainings', function (Blueprint $table) {
            $table->string('label', 50)->change();
        });

        DB::table('hasil_klasifikasis')
            ->where('label_prediksi', 'Menyimpang')
            ->update(['label_prediksi' => 'Tidak Sesuai']);

        DB::table('data_trainings')
            ->where('label', 'Menyimpang')
            ->update(['label' => 'Tidak Sesuai']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('hasil_klasifikasis')
            ->where('label_prediksi', 'Tidak Sesuai')
            ->update(['label_prediksi' => 'Menyimpang']);

        DB::table('data_trainings')
            ->where('label', 'Tidak Sesuai')
            ->update(['label' => 'Menyimpang']);
    }
};
