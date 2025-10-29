<?php

use Tests\TestCase; // <--- PASTIKAN BARIS INI ADA
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Anggota;
use App\Models\Ternak;
use App\Models\Pencatatan;
use App\Models\Tahap;

class AnggotaTest extends TestCase
{
    use RefreshDatabase;


    protected $tahap; 

    protected function setUp(): void 
    {
        parent::setUp();
        $this->tahap = Tahap::factory()->create();
    }

    
     // pengujian hitung jumlah harga awal induk
     /** @test */
    public function harga_induk_counts_only_valid()
    {
        $anggota = Anggota::factory()->create(['tahap_id' => $this->tahap->id]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'aktif',
            'harga' => 1000
        ]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'aktif',
            'harga' => 2000
        ]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Anak',
            'status_aktif' => 'aktif',
            'harga' => 500
        ]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'mati',
            'harga' => 3000
        ]);

        $anggota->refresh();

        $this->assertEquals(3000, $anggota->total_harga_induk);
    }


    // Tes skenario "nol" saat semua ternak tidak valid (Anak/mati).
    /** @test */
    public function harga_induk_nol_if_all_invalid()
    {
       
        $anggota = Anggota::factory()->create(['tahap_id' => $this->tahap->id]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Anak',
            'status_aktif' => 'aktif',
            'harga' => 500
        ]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'mati',
            'harga' => 3000
        ]);

        $anggota->refresh();

        $this->assertEquals(0, $anggota->total_harga_induk);
    }
}