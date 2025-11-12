<?php

use Tests\TestCase; 
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
    public function total_harga_induk_counts_only_valid()
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

    // Memastikan harga induk default-nya nol jika tidak ada ternak.
    /** @test */
    public function total_harga_induk_is_zero_when_no_ternak_exists()
    {
        $anggota = Anggota::factory()->create(['tahap_id' => $this->tahap->id]);

        $this->assertEquals(0.0, $anggota->total_harga_induk);
    }

    // // Memastikan ternak non-Induk tidak dihitung.
    /** @test */
    public function total_harga_induk_is_zero_when_only_non_induk_exists()
    {
        $anggota = Anggota::factory()->create(['tahap_id' => $this->tahap->id]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Anak', 
            'status_aktif' => 'aktif',
            'harga' => 5000 
        ]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Anak', 
            'status_aktif' => 'aktif',
            'harga' => 10000 
        ]);
  
        $anggota->refresh();
  
        $this->assertEquals(0.0, $anggota->total_harga_induk);
    }

    // Memastikan ternak Induk dengan status non-aktif (mati/terjual) tidak dihitung.
    /** @test */
    public function total_harga_induk_is_zero_when_only_non_active_induk_exists()
    {
        $anggota = Anggota::factory()->create(['tahap_id' => $this->tahap->id]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'mati', 
            'harga' => 15000 
        ]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'terjual', 
            'harga' => 20000 
        ]);

        $anggota->refresh();
  
        $this->assertEquals(0.0, $anggota->total_harga_induk);
    }
}