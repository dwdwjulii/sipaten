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
        Schema::create('arsips', function (Blueprint $table) {
            $table->ulid('id')->primary();
            // $table->foreignUlid('tahap_id')->constrained('tahaps')->onDelete('cascade'); // <-- HAPUS BARIS INI
            $table->foreignUlid('diarsipkan_oleh')->constrained('users')->onDelete('cascade');
            $table->string('nama_file');
            $table->string('path_file');
            $table->integer('bulan');
            $table->integer('tahun'); // Ini untuk menyimpan data mentah jika perlu
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('arsips');
    }
};
