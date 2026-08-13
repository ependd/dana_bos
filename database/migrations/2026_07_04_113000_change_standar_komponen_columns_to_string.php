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
        // Ubah kolom di tabel rkas_items menjadi string
        Schema::table('rkas_items', function (Blueprint $table) {
            $table->string('standar_komponen_s_sebelum')->nullable()->change();
            $table->string('standar_komponen_k_sebelum')->nullable()->change();
            $table->string('standar_komponen_s_setelah')->nullable()->default(null)->change();
            $table->string('standar_komponen_k_setelah')->nullable()->default(null)->change();
        });

        // Ubah kolom di tabel data_training_rkas menjadi string
        Schema::table('data_training_rkas', function (Blueprint $table) {
            $table->string('standar_komponen_s_sebelum')->nullable()->change();
            $table->string('standar_komponen_k_sebelum')->nullable()->change();
            $table->string('standar_komponen_s_setelah')->nullable()->change();
            $table->string('standar_komponen_k_setelah')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rkas_items', function (Blueprint $table) {
            $table->decimal('standar_komponen_s_sebelum', 15, 2)->nullable()->change();
            $table->decimal('standar_komponen_k_sebelum', 15, 2)->nullable()->change();
            $table->decimal('standar_komponen_s_setelah', 15, 2)->default(0)->change();
            $table->decimal('standar_komponen_k_setelah', 15, 2)->default(0)->change();
        });

        Schema::table('data_training_rkas', function (Blueprint $table) {
            $table->decimal('standar_komponen_s_sebelum', 15, 2)->nullable()->change();
            $table->decimal('standar_komponen_k_sebelum', 15, 2)->nullable()->change();
            $table->decimal('standar_komponen_s_setelah', 15, 2)->nullable()->change();
            $table->decimal('standar_komponen_k_setelah', 15, 2)->nullable()->change();
        });
    }
};
