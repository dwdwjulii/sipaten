<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArsipFactory extends Factory {
    public function definition(): array {
        return [
            // 'diarsipkan_oleh' butuh ID user, kita buat user admin
            'diarsipkan_oleh' => User::factory()->create( [ 'role' => 'admin' ] ),
            'nama_file' => $this->faker->word() . '.pdf',
            'path_file' => 'arsip/' . $this->faker->uuid() . '.pdf',
            'bulan' => now()->month,
            'tahun' => now()->year,
        ];
    }
}