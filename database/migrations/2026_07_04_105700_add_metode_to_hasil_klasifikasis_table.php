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
        Schema::table('hasil_klasifikasis', function (Blueprint $table) {
            $table->string('metode')->default('woa')->after('periode_bos_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_klasifikasis', function (Blueprint $table) {
            $table->dropColumn('metode');
        });
    }
};
