<?php

namespace Database\Factories;

use App\Models\Pencatatan;
use App\Models\Ternak;
use Illuminate\Database\Eloquent\Factories\Factory;

class PencatatanDetailFactory extends Factory {
    public function definition(): array {
        return [
            'pencatatan_id' => Pencatatan::factory(),
            'ternak_id' => Ternak::factory(),
            'umur_saat_dicatat' => $this->faker->word(),
            'kondisi_ternak' => 'Sehat',
            'status_vaksin' => 'Sudah',
        ];
    }
}