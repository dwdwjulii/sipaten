<?php

namespace Tests\Feature;

use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Pencatatan;
use App\Models\PencatatanDetail;
use App\Models\Tahap;
use App\Models\Ternak;
use App\Models\User;
use Carbon\Carbon; 
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile; 
use Illuminate\Support\Facades\Storage; 
use Tests\TestCase;

class PencatatanTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;
    protected Tahap $tahap;
    protected Carbon $siklusDate;

    protected function setUp(): void
    {
        parent::setUp();

        \PDF::shouldReceive('loadView')->andReturnSelf()
            ->shouldReceive('output')->andReturn('mock pdf data');
        Storage::fake('public');

        $this->admin = User::factory()->admin()->create();

        $this->petugas = User::factory()->create([
            'role' => 'petugas'
        ]);
 
        $this->tahap = Tahap::factory()->create([
            'tahun' => 2025,
            'tahap_ke' => 1
        ]);
        
        $this->siklusDate = Carbon::today()->startOfDay();
    }

    
    // Tes ini memastikan Admin berhasil melakukan arsip
    /** @test */
    public function admin_can_archive_completed_reports()
    {

        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder($this->siklusDate)
            ->create([
                'tahap_id' => $this->tahap->id,
                'status' => 'aktif',
            ]);

        $placeholder = $anggota->latestPencatatan;
        $ternak = $anggota->ternaks->first();
        $this->assertNotNull($placeholder);
        $this->assertNotNull($ternak);

        PencatatanDetail::factory()->create([
            'pencatatan_id' => $placeholder->id,
            'ternak_id' => $ternak->id,
            'kondisi_ternak' => 'Sehat', 
        ]);
     
        $response = $this->actingAs($this->admin)
                        ->post(route('laporan.arsip.keseluruhan'));

        $response->assertRedirect();
        $response->assertSessionHas('success'); 

        $this->assertDatabaseCount('arsips', 1);

        $this->assertDatabaseHas('pencatatans', [
            'id' => $placeholder->id,
            'is_locked' => true 
        ]);
    }
    

    // Tes ini memastikan Admin GAGAL mengarsipkan jika ada anggota aktif yang pencatatan detailnya kosong
    /** @test */
    public function admin_cannot_archive_if_active_anggota_is_incomplete()
    {
       
        $anggotaA = Anggota::factory()->create(['nama' => 'Anggota Pemblokir', 'status' => 'aktif']);
        Ternak::factory()->create([
            'anggota_id' => $anggotaA->id, 
            'status_aktif' => 'aktif', 
            'tipe_ternak' => 'Induk'
        ]);
        Pencatatan::factory()->create([
            'anggota_id' => $anggotaA->id,
            'tanggal_catatan' => $this->siklusDate,
            'is_locked' => false,
        ]);
        
        $anggotaB = Anggota::factory()->create(['nama' => 'Anggota Lengkap', 'status' => 'aktif']);
        $ternakB = Ternak::factory()->create([
            'anggota_id' => $anggotaB->id, 
            'status_aktif' => 'aktif'
        ]);
        $pencatatanB = Pencatatan::factory()->create([
            'anggota_id' => $anggotaB->id,
            'tanggal_catatan' => $this->siklusDate,
            'is_locked' => false,
        ]);
        PencatatanDetail::factory()->create(['pencatatan_id' => $pencatatanB->id, 'ternak_id' => $ternakB->id, 'kondisi_ternak' => 'Sehat']);

        $response = $this->actingAs($this->admin)->post(route('laporan.arsip.keseluruhan'));

        $response->assertStatus(302); 
        $response->assertSessionHas('error', 'Gagal, masih ada data aktif yang belum dilengkapi oleh petugas.');
        
        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggotaA->id,
            'is_locked' => false,
        ]);
 
        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggotaB->id,
            'is_locked' => false,
        ]);
  
        $this->assertDatabaseCount('arsips', 0);
    }
    

    // Tes ini memastikan Admin BERHASIL "Mulai Periode Baru" (reset)
    /** @test */
    public function admin_can_reset_period_after_archive_is_done()
    {
        // ARRANGE
        $anggota = Anggota::factory()->create([
            'tahap_id' => $this->tahap->id,
            'status' => 'aktif',
            'jumlah_induk' => 1
        ]);
        
        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'aktif' 
        ]);

        $anggotaNonAktif = Anggota::factory()->nonaktif()->create([
            'tahap_id' => $this->tahap->id
        ]);

        $placeholderLama = Pencatatan::factory()->create([
            'anggota_id' => $anggota->id,
            'is_locked' => true, 
            'tanggal_catatan' => now()->subMonth() 
        ]);

        $this->assertDatabaseMissing('pencatatans', ['is_locked' => false]);

        $response = $this->actingAs($this->admin)
                        ->post(route('pencatatan.reset')); 

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false 
        ]);

        $this->assertDatabaseMissing('pencatatans', [
            'anggota_id' => $anggotaNonAktif->id,
            'is_locked' => false 
        ]);
        
        $this->assertDatabaseHas('pencatatans', [
            'id' => $placeholderLama->id,
            'is_locked' => true
        ]);
    }
    
    // Tes ini memastikan petugas bisa menyimpan data pencatatan 
    /** @test */
    public function petugas_can_store_pencatatan_for_anggota()
    {

        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder($this->siklusDate) 
            ->create([
                'tahap_id' => $this->tahap->id,
                'jumlah_induk' => 1 
            ]);

        $placeholder = $anggota->latestPencatatan;
        $induk = $anggota->ternaks->where('tipe_ternak', 'Induk')->first();
        $this->assertNotNull($placeholder);
        $this->assertNotNull($induk);
        $this->assertEquals(1, $anggota->ternaks->count()); 

        $pencatatanData = [
            'pencatatan_id' => $placeholder->id, 
            'anggota_id' => $anggota->id,
            'temuan_lapangan' => 'Semua ternak dalam kondisi baik.',
            'ternaks' => [
                $induk->id => [ 
                    'ternak_id' => $induk->id, 
                    'tipe_ternak' => 'Induk',
                    'no_ear_tag' => 'Ada', 
                    'jenis_kelamin' => 'Betina',
                    'umur_ternak' => '2 Tahun',
                    'kondisi_ternak' => 'Sehat', 
                    'status_vaksin' => 'Sudah',
                    'induk_id' => null, 
                ],
            ],
        ];

        $response = $this->actingAs($this->petugas)
                        ->post(route('pencatatan.store'), $pencatatanData);

        $response->assertRedirect(route('pencatatan.index'));
        $response->assertSessionHas('success', 'Catatan berhasil disimpan!');

        $this->assertDatabaseHas('pencatatans', [
            'id' => $placeholder->id,
            'petugas_id' => $this->petugas->id, 
            'temuan_lapangan' => 'Semua ternak dalam kondisi baik.',
            'is_locked' => false 
        ]);

        $this->assertDatabaseHas('pencatatan_details', [
            'pencatatan_id' => $placeholder->id,
            'ternak_id' => $induk->id,
            'umur_saat_dicatat' => '2 Tahun',
            'kondisi_ternak' => 'Sehat',
            'status_vaksin' => 'Sudah',
        ]);

        $this->assertDatabaseHas('ternaks', [
            'id' => $induk->id,
            'no_ear_tag' => 'Ada', 
            'status_aktif' => 'aktif' 
        ]);
        
         $this->assertDatabaseHas('anggotas', [
             'id' => $anggota->id,
             'jumlah_induk' => 1 
         ]);
    }

    
    //Tes ini memastikan bahwa jika petugas melaporkan ternak 'Mati'/'Terjual',
    // record Ternak master di-update statusnya menjadi 'nonaktif'.
    /** @test */
    public function store_sets_ternak_nonaktif_when_mati_or_terjual() 
    {

        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder($this->siklusDate)
            ->create([
                'tahap_id' => $this->tahap->id,
                'jumlah_induk' => 1
            ]);

        $placeholder = $anggota->latestPencatatan;
        $induk = $anggota->ternaks->where('tipe_ternak', 'Induk')->first();
        $this->assertNotNull($placeholder);
        $this->assertNotNull($induk);
        $this->assertEquals('aktif', $induk->status_aktif);

        $pencatatanData = [
            'pencatatan_id' => $placeholder->id,
            'anggota_id' => $anggota->id,
            'temuan_lapangan' => 'Induk ditemukan mati.',
            'ternaks' => [
                $induk->id => [
                    'ternak_id' => $induk->id,
                    'tipe_ternak' => 'Induk',
                    'no_ear_tag' => $induk->no_ear_tag, 
                    'jenis_kelamin' => 'Betina',
                    'umur_ternak' => '2.5 Tahun',
                    'kondisi_ternak' => 'Mati',
                    'status_vaksin' => 'Sudah',
                    'induk_id' => null,
                ],
            ],
        ];

        $response = $this->actingAs($this->petugas)
                        ->post(route('pencatatan.store'), $pencatatanData);

        $response->assertRedirect(route('pencatatan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pencatatan_details', [
            'pencatatan_id' => $placeholder->id,
            'ternak_id' => $induk->id,
            'kondisi_ternak' => 'Mati',
        ]);

        $this->assertDatabaseHas('ternaks', [
            'id' => $induk->id,
            'status_aktif' => 'nonaktif' 
        ]);
    }

}