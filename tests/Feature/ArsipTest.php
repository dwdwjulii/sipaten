<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;      // Pastikan Anda mengimpor model User Anda
use App\Models\Arsip;
use App\Models\Pencatatan;
use Tests\TestCase;
use App\Models\Tahap;   // Tambahkan ini
use App\Models\Anggota;  // Tambahkan ini

class ArsipTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');

        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }
   
    /** @test */
    public function admin_can_show_archive_file()
    {

        $dummyContent = 'Ini adalah konten PDF palsu.';
        $filePath = 'arsip/dummy-file-test.pdf';

        Storage::disk('local')->put($filePath, $dummyContent);

        $arsip = Arsip::factory()->create([
            'path_file' => $filePath,
        ]);

        $response = $this->actingAs($this->admin)->get(route('arsip.show', $arsip));

        $response->assertStatus(200);
        
        $response->assertHeader('Content-Type', 'application/pdf');
        
        $this->assertEquals($dummyContent, $response->streamedContent());
    }

    /** @test */
    public function admin_can_destroy_archive_and_unlocks_related_pencatatan()
    {
        
        $tahun = 2024;
        $bulan = 10;
        $filePath = 'arsip/file-untuk-dihapus.pdf';

        Storage::disk('local')->put($filePath, 'konten hapus');
        
        $arsip = Arsip::factory()->create([
            'tahun' => $tahun,
            'bulan' => $bulan,
            'path_file' => $filePath,
        ]);
        
        $tahap = Tahap::factory()->create();
        $anggota = Anggota::factory()->create([
            'tahap_id' => $tahap->id,
        ]);

        $pencatatanTerkunci = Pencatatan::factory()->create([
            'anggota_id' => $anggota->id, 
            'tanggal_catatan' => "{$tahun}-{$bulan}-15",
            'is_locked' => true
        ]);
        
        DB::table('status_tahunan')->insert(['tahun' => $tahun, 'status' => 'selesai']);

        $response = $this->actingAs($this->admin)->delete(route('arsip.destroy', $arsip));
   
        $response->assertRedirect(route('arsip.tahun', $tahun));
        $response->assertSessionHas('success');

        Storage::disk('local')->assertMissing($filePath);

        $this->assertModelMissing($arsip); 

        $this->assertDatabaseHas('pencatatans', [
            'id' => $pencatatanTerkunci->id,
            'is_locked' => false
        ]);

        $this->assertDatabaseHas('status_tahunan', [
            'tahun' => $tahun,
            'status' => 'progress'
        ]);
    }

    /** @test */
    public function destroy_does_not_reset_status_if_other_arsip_exists()
    {
        // ----- ARRANGE -----
        $tahun = 2024;
        
        // Buat 2 arsip di tahun yang sama
        $arsipDihapus = Arsip::factory()->create(['tahun' => $tahun, 'bulan' => 1]);
        $arsipSisa = Arsip::factory()->create(['tahun' => $tahun, 'bulan' => 2]);
        
        // Set status 'selesai'
        DB::table('status_tahunan')->insert(['tahun' => $tahun, 'status' => 'selesai']);

        // ----- ACT -----
        $this->actingAs($this->admin)->delete(route('arsip.destroy', $arsipDihapus));
        
        // ----- ASSERT -----
        // 1. Assert arsip telah terhapus
        $this->assertModelMissing($arsipDihapus);
        
        // 2. Assert status tahunan TETAP 'selesai' karena masih ada $arsipSisa
        $this->assertDatabaseHas('status_tahunan', [
            'tahun' => $tahun,
            'status' => 'selesai'
        ]);
    }

    // -----------------------------------------------------------------
    // TEST UNTUK METHOD validasi()
    // -----------------------------------------------------------------

    /** @test */
    public function admin_can_validate_arsip_tahunan()
    {
        // ----- ARRANGE -----
        $tahun = 2024;
        
        // Case 1: Insert baru (belum ada record status_tahunan untuk 2024)
        
        // ----- ACT -----
        // Asumsi route 'arsip.validasi' menggunakan method POST atau PATCH
        // Saya gunakan PATCH di sini. Sesuaikan jika Anda menggunakan POST.
        $responseInsert = $this->actingAs($this->admin)
                       ->post(route('arsip.validasi', $tahun));

        // ----- ASSERT (Case 1) -----
        $responseInsert->assertRedirect(route('arsip.index'));
        $responseInsert->assertSessionHas('success');
        $this->assertDatabaseHas('status_tahunan', [
            'tahun' => $tahun,
            'status' => 'selesai'
        ]);

        // ----- ARRANGE (Case 2) -----
        // Case 2: Update (record sudah ada dengan status 'progress')
        $tahunUpdate = 2023;
        DB::table('status_tahunan')->insert([
            'tahun' => $tahunUpdate,
            'status' => 'progress'
        ]);
        
        // ----- ACT (Case 2) -----
        $responseUpdate = $this->actingAs($this->admin)
                       ->post(route('arsip.validasi', $tahunUpdate));
        
        // ----- ASSERT (Case 2) -----
        $responseUpdate->assertRedirect(route('arsip.index'));
        $this->assertDatabaseHas('status_tahunan', [
            'tahun' => $tahunUpdate,
            'status' => 'selesai' // Memastikan status ter-update
        ]);
        
        // Memastikan tidak ada duplikat record
        $this->assertEquals(1, DB::table('status_tahunan')->where('tahun', $tahunUpdate)->count());
    }
}