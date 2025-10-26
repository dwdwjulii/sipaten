<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PencatatanFactory extends Factory {
    public function definition(): array {
        return [
            'anggota_id' => Anggota::factory(),
            // Dibuat null dulu, seolah-olah placeholder
            'petugas_id' => null,
            'tanggal_catatan' => now(),
            'temuan_lapangan' => null,
            'is_locked' => false, // Default: siklus aktif
        ];
    }
}