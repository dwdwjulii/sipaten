<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Panggil UserSeeder yang baru kita buat
        $this->call([
            UserSeeder::class,    // Membuat data user (jika ada)
          //  TahapSeeder::class,   // WAJIB: Membuat data tahap terlebih dahulu
          //  AnggotaSeeder::class, // Baru membuat data anggota yang terkait dengan tahap
        ]);

        // Anda bisa memanggil seeder lain di sini nanti
        // $this->call(NamaSeederLain::class);
    }
}