<?php

namespace Database\Factories;

use App\Models\Anggota;
use App\Models\Ternak;
use App\Models\Pencatatan;
use App\Models\Tahap;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnggotaFactory extends Factory
{
    protected $model = Anggota::class;

    public function definition(): array
    {
        $jumlahInduk = $this->faker->numberBetween(1, 3);

        return [
             'tahap_id' => Tahap::factory(),
            'nama' => $this->faker->name(),
            'jenis_ternak' => $this->faker->randomElement(['Sapi', 'Kambing']),
            'jumlah_induk' => $jumlahInduk,
            'tempat_lahir' => $this->faker->city(),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-40 years', '-20 years')->format('Y-m-d'),
            'no_hp' => $this->faker->unique()->phoneNumber(),
            'lokasi_kandang' => $this->faker->address(),
            'status' => 'aktif', // Kita ubah default-nya jadi 'aktif' agar mudah diprediksi
        ];
    }

    /**
     * HAPUS FUNGSI configure() YANG LAMA DARI SINI
     * Logika di dalamnya kita pindahkan ke state di bawah
     */

    /**
     * State untuk mengubah status menjadi non-aktif
     */
    public function nonaktif(): Factory
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'non-aktif',
        ]);
    }

    /**
     * State baru untuk membuat anggota LENGKAP DENGAN ternak & placeholder.
     * Ini TIDAK akan berjalan otomatis.
     */
    public function withPencatatanPlaceholder(): Factory
    {
        // afterCreating() sekarang ada DI DALAM state ini
        return $this->afterCreating(function (Anggota $anggota) {
            
            // LOGIKA 1: Buat data ternak awal (induk)
            for ($i = 0; $i < $anggota->jumlah_induk; $i++) {
                $anggota->ternaks()->create([
                    'tipe_ternak' => 'Induk',
                    'harga' => $this->faker->numberBetween(7000000, 15000000),
                    'no_ear_tag' => 'Tidak Ada',
                    'jenis_kelamin' => 'Betina',
                ]);
            }

            // LOGIKA 2: Buat data pencatatan awal jika status anggota adalah 'aktif'
            if ($anggota->status === 'aktif') {
                Pencatatan::factory()->create([
                    'anggota_id' => $anggota->id, // Kita berikan ID-nya
                    'tanggal_catatan' => now(),
                    'is_locked' => false,
                ]);
            }
        });
    }
}