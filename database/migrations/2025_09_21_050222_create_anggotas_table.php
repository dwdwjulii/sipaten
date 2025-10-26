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
        Schema::create('anggotas', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('tahap_id')
                  ->references('id')
                  ->on('tahaps')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->string('nama');
            $table->enum('jenis_ternak', ['Kambing', 'Sapi']);
            $table->integer('jumlah_induk')->default(0);
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('no_hp');
            $table->string('lokasi_kandang');
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anggotas');
    }
};
