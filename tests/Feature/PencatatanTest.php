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

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();

        $this->petugas = User::factory()->create([
            'role' => 'petugas'
        ]);
 
        $this->tahap = Tahap::factory()->create([
            'tahun' => 2025,
            'tahap_ke' => 1
        ]);
    }

    //Tes ini memastikan Admin GAGAL melakukan "Tutup Buku" (arsip)
    /** @test */
    public function test_admin_cannot_archive_if_petugas_has_not_filled_report()
    {
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() 
            ->create([
                'tahap_id' => $this->tahap->id,
                'status' => 'aktif',
            ]);

        $placeholder = $anggota->latestPencatatan;
        $this->assertNotNull($placeholder, 'Placeholder pencatatan gagal dibuat oleh factory');
        $this->assertDatabaseMissing('pencatatan_details', [
            'pencatatan_id' => $placeholder->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->post(route('laporan.arsip.keseluruhan')); 

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Gagal, masih ada data aktif yang belum dilengkapi oleh petugas.');

        $this->assertDatabaseCount('arsips', 0);

        $this->assertDatabaseHas('pencatatans', [
            'id' => $placeholder->id,
            'is_locked' => false 
        ]);
    }

    //Tes ini memastikan Admin BERHASIL melakukan "Tutup Buku" (arsip)
    /** @test */
    public function test_admin_can_archive_completed_reports()
    {
 
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() 
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

  
    // Tes ini memastikan Admin BERHASIL "Mulai Periode Baru" (reset)
    /** @test */
    public function test_admin_can_reset_period_after_archive_is_done()
    {
 
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
    
    // Tes ini memastikan petugas bisa menyimpan data pencatatan dasar
    /** @test */
    public function test_petugas_can_store_pencatatan_for_anggota()
    {
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() 
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
    public function test_store_sets_ternak_nonaktif_when_mati_or_terjual() 
    {

        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder()
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

   
    // Tes ini memastikan petugas bisa mengubah status ternak 'Anak' menjadi 'Induk'
    /** @test */
    public function test_update_can_promote_anak_to_induk()
    {
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder()
            ->create([
                'tahap_id' => $this->tahap->id,
                'jumlah_induk' => 1 
            ]);

        $placeholder = $anggota->latestPencatatan;
        $indukAsli = $anggota->ternaks->where('tipe_ternak', 'Induk')->first();

        $anak = Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'induk_id' => $indukAsli->id, 
            'tipe_ternak' => 'Anak',
            'status_aktif' => 'aktif',
        ]);

        $detailAnakLama = PencatatanDetail::factory()->create([
            'pencatatan_id' => $placeholder->id,
            'ternak_id' => $anak->id,
            'umur_saat_dicatat' => '6 Bulan',
            'kondisi_ternak' => 'Sehat',
            'status_vaksin' => 'Belum',
        ]);
 
         PencatatanDetail::factory()->create([
             'pencatatan_id' => $placeholder->id,
             'ternak_id' => $indukAsli->id,
         ]);

        $updateData = [
            'temuan_lapangan' => 'Anak sudah dewasa, dijadikan induk baru.',
            'ternaks' => [
                 $indukAsli->id => [
                     'detail_id' => $placeholder->details->where('ternak_id', $indukAsli->id)->first()->id,
                     'tipe_ternak' => 'Induk',
                     'no_ear_tag' => 'Ada',
                     'jenis_kelamin' => 'Betina',
                     'umur_ternak' => '3 Tahun',
                     'kondisi_ternak' => 'Sehat',
                     'status_vaksin' => 'Sudah',
                     'induk_id' => null,
                 ],
                 $anak->id => [
                     'detail_id' => $detailAnakLama->id, 
                     'tipe_ternak' => 'Induk', 
                     'no_ear_tag' => $anak->no_ear_tag,
                     'jenis_kelamin' => $anak->jenis_kelamin, 
                     'umur_ternak' => '1.5 Tahun', 
                     'kondisi_ternak' => 'Sehat',
                     'status_vaksin' => 'Sudah',
                 ],
            ],
        ];

        $response = $this->actingAs($this->petugas)
                         ->put(route('pencatatan.update', $placeholder->id), $updateData);
        $response->assertRedirect(route('pencatatan.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('pencatatan_details', [
            'id' => $detailAnakLama->id,
            'umur_saat_dicatat' => '1.5 Tahun',
            'status_vaksin' => 'Sudah',
        ]);

        $this->assertDatabaseHas('ternaks', [
            'id' => $anak->id, 
            'tipe_ternak' => 'Induk', 
            'induk_id' => null 
        ]);
        
         $this->assertDatabaseHas('anggotas', [
             'id' => $anggota->id,
             'jumlah_induk' => 2 
         ]);
    }
}