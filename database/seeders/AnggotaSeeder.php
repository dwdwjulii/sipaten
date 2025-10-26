<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Anggota;
use App\Models\Tahap; // Import model Tahap untuk mengambil datanya

class AnggotaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Cari tahap yang ingin diisi anggota
        // Kita cari tahap yang baru saja dibuat di TahapSeeder
      // $tahap = Tahap::where('tahun', 2024)->where('tahap_ke', 1)->first();

        // 2. Pastikan tahap ditemukan sebelum membuat anggota
      //  if ($tahap) {
            // 3. Buat anggota menggunakan factory dan langsung hubungkan dengan tahap_id
        //    Anggota::factory(0)->create([
        //        'tahap_id' => $tahap->id,
         //   ]);
     //   } else {
            // Beri pesan jika tahap tidak ditemukan, agar tidak ada error
       //     $this->command->warn('Tahap untuk tahun 2024 dan tahap ke 1 tidak ditemukan. Anggota tidak dibuat.');
       // }
    }
}