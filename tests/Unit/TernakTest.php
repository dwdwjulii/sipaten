<?php

namespace Tests\Unit;

use Tests\TestCase; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Anggota;
use App\Models\Ternak;
use App\Models\Tahap;

class TernakTest extends TestCase
{
    use RefreshDatabase;

    protected $anggota;

    protected function setUp(): void
    {
        parent::setUp();
        $tahap = Tahap::factory()->create();
        $this->anggota = Anggota::factory()->create(['tahap_id' => $tahap->id]);
    }

    /**
     * Tes ini memverifikasi relasi Anak -> Induk ('induk()').
     * @test
     */
    public function test_relasi_anak_ke_induk_is_valid()
    {
        $induk = Ternak::factory()->create([
            'anggota_id' => $this->anggota->id,
            'tipe_ternak' => 'Induk'
        ]);

        $anak = Ternak::factory()->create([
            'anggota_id' => $this->anggota->id,
            'tipe_ternak' => 'Anak',
            'induk_id' => $induk->id 
        ]);

        $retrievedInduk = $anak->induk;
        $this->assertNotNull($retrievedInduk);
        $this->assertEquals($induk->id, $retrievedInduk->id);
        $this->assertInstanceOf(Ternak::class, $retrievedInduk);
    }

}