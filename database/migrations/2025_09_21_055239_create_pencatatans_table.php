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
        Schema::create('pencatatans', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('anggota_id')->constrained('anggotas')->onDelete('cascade');
            $table->foreignUlid('petugas_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->date('tanggal_catatan')->useCurrent();
            $table->text('temuan_lapangan')->nullable();
            $table->json('foto_dokumentasi')->nullable();
            $table->boolean('is_locked')->default(false); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencatatans');
    }
};