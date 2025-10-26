<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tahap; // Pastikan model Tahap di-import

class TahapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data tahap lama agar tidak ada duplikasi jika seeder dijalankan ulang
      //  Tahap::query()->delete();

        // Membuat satu data tahap sesuai dengan validasi di controller
      //  Tahap::create([
           // 'tahun' => 2024, // Contoh tahun, harus integer
          //  'tahap_ke' => 1,  // Contoh tahap ke, harus integer
      // ]);

        // Anda bisa menambahkan tahap lain jika perlu
        // Tahap::create(['tahun' => 2024, 'tahap_ke' => 2]);
        // Tahap::create(['tahun' => 2025, 'tahap_ke' => 1]);
    }
}