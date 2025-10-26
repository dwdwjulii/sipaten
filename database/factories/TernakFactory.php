<?php

namespace Database\Factories;

use App\Models\Anggota;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ternak>
 */
class TernakFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Kita butuh Anggota untuk setiap Ternak
            'anggota_id' => Anggota::factory(), 
            
            // Kita set default sebagai Induk Betina, sesuai AnggotaFactory Anda
            'tipe_ternak' => 'Induk',
            'jenis_kelamin' => 'Betina',
            'no_ear_tag' => $this->faker->randomElement(['Ada', 'Tidak Ada']),
            'status_aktif' => 'aktif',
            'harga' => $this->faker->numberBetween(7000000, 15000000),
            
            // Biarkan 'induk_id' null secara default (karena ini Induk)
            'induk_id' => null, 
        ];
    }
}