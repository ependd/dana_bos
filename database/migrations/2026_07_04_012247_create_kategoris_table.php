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
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        // Insert default categories
        $defaultCategories = [
            ['nama' => 'ATK', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Bahan Ajar', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Peralatan Lab', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'TIK', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Mebeler', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Pemeliharaan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Kegiatan', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Honorarium', 'created_at' => now(), 'updated_at' => now()],
            ['nama' => 'Lainnya', 'created_at' => now(), 'updated_at' => now()],
        ];

        DB::table('kategoris')->insert($defaultCategories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
