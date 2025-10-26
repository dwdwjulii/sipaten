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
        Schema::create('pencatatan_details', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('pencatatan_id')->constrained('pencatatans')->onDelete('cascade');
            $table->foreignUlid('ternak_id')->constrained('ternaks')->onDelete('cascade');
            $table->string('umur_saat_dicatat');
            $table->string('kondisi_ternak');
            $table->string('status_vaksin');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencatatan_details');
    }
};
