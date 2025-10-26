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
        Schema::create('tahaps', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->year('tahun');
            $table->integer('tahap_ke');
            $table->timestamps();

            // Mencegah duplikasi data, contoh: "Tahap 1 (2025)" hanya boleh ada satu.
            $table->unique(['tahun', 'tahap_ke']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahaps');
    }
};