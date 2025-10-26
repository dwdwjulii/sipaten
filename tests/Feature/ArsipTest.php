<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; // Untuk status_tahunan
use App\Models\Tahap; // Tambahkan import Tahap
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Arsip;
use App\Models\Pencatatan;
// Memastikan semua factory dan model ada
// use Database\Factories\PencatatanFactory; // Asumsi sudah di-import di atas

class ArsipTest extends TestCase {
    use RefreshDatabase;

    private function createAdmin(): User {
        return User::factory()->create( [ 'role' => 'admin' ] );
    }
    /** @test */
    public function test_destroy_resets_yearly_status_if_last_archive_is_deleted(): void 
    {
        // 1. ARRANGE
        $admin = $this->createAdmin();
        Storage::fake('public');
        $tahun = 2024;

        // Setup: Buat dua arsip di tahun yang sama
        $arsip1 = Arsip::factory()->create(['tahun' => $tahun, 'bulan' => 1, 'path_file' => 'arsip/file1.pdf']);
        $arsip2 = Arsip::factory()->create(['tahun' => $tahun, 'bulan' => 2, 'path_file' => 'arsip/file2.pdf']);
        
        Storage::disk('public')->put($arsip1->path_file, 'content');
        
        // Setup: Atur status tahunan menjadi 'selesai'
        DB::table('status_tahunan')->insert([
            'tahun' => $tahun,
            'status' => 'selesai',
        ]);
        
        // 2. ACT - Hapus arsip pertama (arsip2 tetap ada)
        $this->actingAs($admin)->delete(route('arsip.destroy', $arsip2));
        
        // ASSERT A: Status tahunan HARUS tetap 'selesai' (bukan arsip terakhir)
        $this->assertDatabaseHas('status_tahunan', ['tahun' => $tahun, 'status' => 'selesai']);
        
        // 3. ACT - Hapus arsip kedua (arsip1)
        $this->actingAs($admin)->delete(route('arsip.destroy', $arsip1));
        
        // ASSERT B: Status tahunan HARUS kembali ke 'progress' (arsip terakhir telah dihapus)
        $this->assertDatabaseHas('status_tahunan', ['tahun' => $tahun, 'status' => 'progress']);
    }

    /** @test */
    public function test_validasi_manages_yearly_status_correctly(): void
    {
        $admin = $this->createAdmin();
        $tahunTarget = 2023;

        // 1. ACT - Validasi tahun yang belum ada (INSERT)
        $response1 = $this->actingAs($admin)->post(route('arsip.validasi', ['tahun' => $tahunTarget]));

        // ASSERT 1: Record baru dibuat dengan status 'selesai'
        $response1->assertRedirect(route('arsip.index'));
        $this->assertDatabaseHas('status_tahunan', ['tahun' => $tahunTarget, 'status' => 'selesai']);

        // 2. ACT - Validasi tahun yang sudah ada (UPDATE)
        $response2 = $this->actingAs($admin)->post(route('arsip.validasi', ['tahun' => $tahunTarget]));
        
        // ASSERT 2: Status tetap 'selesai' (memastikan updateOrInsert bekerja)
        $response2->assertRedirect(route('arsip.index'));
        $this->assertDatabaseHas('status_tahunan', ['tahun' => $tahunTarget, 'status' => 'selesai']);
    }
}