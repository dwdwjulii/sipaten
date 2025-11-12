<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\User;    
use App\Models\Arsip;
use App\Models\Pencatatan;
use Tests\TestCase;
use App\Models\Tahap;   
use App\Models\Anggota;  

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
    public function destroy_archive_and_unlocks_related_pencatatan()
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
    //public function destroy_does_not_reset_status_if_other_arsip_exists()
    //{

      //  $tahun = 2024;

      //  $arsipDihapus = Arsip::factory()->create(['tahun' => $tahun, 'bulan' => 1]);
      //  $arsipSisa = Arsip::factory()->create(['tahun' => $tahun, 'bulan' => 2]);
 
      //  DB::table('status_tahunan')->insert(['tahun' => $tahun, 'status' => 'selesai']);

       // $this->actingAs($this->admin)->delete(route('arsip.destroy', $arsipDihapus));
  
      //  $this->assertModelMissing($arsipDihapus);

       // $this->assertDatabaseHas('status_tahunan', [
      //      'tahun' => $tahun,
      //      'status' => 'selesai'
       // ]);
   // }

  
    /** @test */
    public function admin_can_validate_arsip_tahunan()
    {

        $tahun = 2024;
        
        $responseInsert = $this->actingAs($this->admin)
                       ->post(route('arsip.validasi', $tahun));

        $responseInsert->assertRedirect(route('arsip.index'));
        $responseInsert->assertSessionHas('success');
        $this->assertDatabaseHas('status_tahunan', [
            'tahun' => $tahun,
            'status' => 'selesai'
        ]);

        $tahunUpdate = 2023;
        DB::table('status_tahunan')->insert([
            'tahun' => $tahunUpdate,
            'status' => 'progress'
        ]);
 
        $responseUpdate = $this->actingAs($this->admin)
                       ->post(route('arsip.validasi', $tahunUpdate));

        $responseUpdate->assertRedirect(route('arsip.index'));
        $this->assertDatabaseHas('status_tahunan', [
            'tahun' => $tahunUpdate,
            'status' => 'selesai' 
        ]);

        $this->assertEquals(1, DB::table('status_tahunan')->where('tahun', $tahunUpdate)->count());
    }
}