<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status_tahunan', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun')->unique(); // Menyimpan tahun, contoh: 2024
            $table->string('status')->default('progress'); // Status default adalah 'progress'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('status_tahunan');
    }
};
