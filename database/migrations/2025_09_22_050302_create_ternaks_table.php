<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_ternaks_table.php
    public function up(): void
    {
        Schema::create('ternaks', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->foreignUlid('induk_id')->nullable()->constrained('ternaks')->onDelete('set null');

            // Kolom Data Master
            $table->enum('tipe_ternak', ['Induk', 'Anak']);
            $table->enum('no_ear_tag', ['Ada', 'Tidak Ada'])->nullable();
            $table->enum('jenis_kelamin', ['Jantan', 'Betina'])->nullable();
            $table->string('status_aktif')->default('aktif');
            $table->decimal('harga', 15, 2)->default(0); // Harga awal saat didaftarkan

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.      
     */
    public function down(): void
    {
        Schema::dropIfExists('ternaks');
    }
};
