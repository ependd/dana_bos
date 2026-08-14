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
        Schema::create('rkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_bos_id')->constrained('periode_bos')->cascadeOnDelete();
            $table->string('kode_rkas')->unique();
            $table->enum('status', ['draft', 'submitted', 'approved', 'rejected', 'revised'])->default('draft');
            $table->decimal('total_anggaran', 15, 2)->default(0);
            $table->text('catatan')->nullable();
            $table->text('catatan_penolakan')->nullable();
            $table->integer('revisi_ke')->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rkas');
    }
};
