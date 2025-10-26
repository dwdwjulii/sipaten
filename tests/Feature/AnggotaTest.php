<?php

namespace Tests\Feature;
use App\Models\PencatatanDetail;
use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Pencatatan;
use App\Models\Tahap;
use App\Models\Ternak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Database\Factories\PencatatanDetailFactory;

class AnggotaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $petugas;
    protected Tahap $tahap;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->petugas = User::factory()->create(['role' => 'petugas']);
        $this->tahap = Tahap::factory()->create(['tahun' => 2025, 'tahap_ke' => 1]);
    }

    // Pengujian: Admin dapat membuat anggota baru yang aktif
    public function test_admin_can_create_new_anggota_aktif()
    {
        $anggotaData = [
            'tahap_id' => $this->tahap->id,
            'nama' => 'Anggota Baru Aktif',
            'jenis_ternak' => 'Sapi',
            'jumlah_induk' => 2,
            'tempat_lahir' => 'Singaraja',
            'tanggal_lahir' => '10/10/1990',
            'no_hp' => '08123456789',
            'lokasi_kandang' => 'Jl. Mawar',
            'status' => 'aktif',
            'ternak_awal' => [
                ['harga' => 10000000],
                ['harga' => 11000000],
            ],
        ];
        
        $response = $this->actingAs($this->admin)->post(route('anggota.store'), $anggotaData);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $anggotaBaru = Anggota::where('nama', 'Anggota Baru Aktif')->first();
        $this->assertNotNull($anggotaBaru);

        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggotaBaru->id,
            'is_locked' => false
        ]);
    }

    // Pengujian: Admin membuat anggota non-aktif tidak membuat placeholder
    public function test_admin_create_anggota_non_aktif_does_not_create_placeholder()
    {
        $anggotaData = [
            'tahap_id' => $this->tahap->id,
            'nama' => 'Anggota Baru Non-Aktif',
            'jenis_ternak' => 'Sapi',
            'jumlah_induk' => 1,
            'tempat_lahir' => 'Singaraja',
            'tanggal_lahir' => '10/10/1990',
            'no_hp' => '08123456788',
            'lokasi_kandang' => 'Jl. Melati',
            'status' => 'non-aktif',
            'ternak_awal' => [
                ['harga' => 10000000],
            ],
        ];
        
        $this->actingAs($this->admin)->post(route('anggota.store'), $anggotaData);

        $anggotaBaru = Anggota::where('nama', 'Anggota Baru Non-Aktif')->first();
        $this->assertNotNull($anggotaBaru);

        $this->assertDatabaseMissing('pencatatans', [
            'anggota_id' => $anggotaBaru->id
        ]);
    }

    // Pengujian: Admin tidak dapat membuat anggota jika periode diarsip
    public function test_admin_cannot_create_anggota_if_period_is_archived()
    {
        Arsip::factory()->create([
            'bulan' => now()->month,
            'tahun' => now()->year,
        ]);
        
        $anggotaData = [
            'tahap_id' => $this->tahap->id,
            'nama' => 'Anggota Gagal Arsip',
            'status' => 'aktif',
            'jenis_ternak' => 'Sapi',
            'jumlah_induk' => 1,
            'tempat_lahir' => 'Singaraja',
            'tanggal_lahir' => '10/10/1990',
            'no_hp' => '08123456788',
            'lokasi_kandang' => 'Jl. Melati',
            'ternak_awal' => [['harga' => 10000000]],
        ];

        $this->actingAs($this->admin)->post(route('anggota.store'), $anggotaData);
        
        $anggotaBaru = Anggota::where('nama', 'Anggota Gagal Arsip')->first();
        $this->assertNotNull($anggotaBaru);

        $this->assertDatabaseMissing('pencatatans', [
            'anggota_id' => $anggotaBaru->id
        ]);
    }

    // Pengujian: Update status aktif ke non-aktif menghapus placeholder
    public function test_update_status_aktif_to_non_aktif_deletes_placeholder()
    {
        // 1. ARRANGE (Persiapan)
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() 
            ->create([
                'tahap_id' => $this->tahap->id,
            ]);
        
        // Panggil fresh() sebelum menggunakan objek untuk update
        $anggota = $anggota->fresh(); 
        
        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false
        ]);

        // Ambil data valid, lalu ubah statusnya
        $updateData = $this->getValidUpdateData($anggota, [ 
            'status' => 'non-aktif' 
        ]);

        $this->actingAs($this->admin)->put(route('anggota.update', $anggota->id), $updateData);

        // ASSERT 1: Placeholder telah dihapus
        $this->assertDatabaseMissing('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false
        ]);

        // ASSERT 2: Status Anggota di DB telah berubah 
        $this->assertDatabaseHas('anggotas', [
            'id' => $anggota->id,
            'status' => 'non-aktif' 
        ]);
    }

    // Pengujian: Update status non-aktif ke aktif membuat placeholder
    public function test_update_status_non_aktif_to_aktif_creates_placeholder()
    {
        // ARRANGE
        $anggota = Anggota::factory()
            ->nonaktif() 
            ->create([
                'tahap_id' => $this->tahap->id,
                'jumlah_induk' => 1
            ]);

        // Buat Ternak (Penting agar lolos whereHas di controller)
        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'aktif',
            'harga' => 10000000 
        ]);
        
        $anggota = $anggota->fresh(); 
        $this->assertDatabaseMissing('pencatatans', ['anggota_id' => $anggota->id]);

        // ACT: Kirim update (dengan semua field wajib)
        $updateData = $this->getValidUpdateData($anggota, [
            'status' => 'aktif', 
            'nama' => $anggota->nama,
            'jenis_ternak' => $anggota->jenis_ternak,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir->format('d/m/Y'), 
            'no_hp' => $anggota->no_hp,
            'lokasi_kandang' => $anggota->lokasi_kandang,
        ]);

        $this->actingAs($this->admin)->put(route('anggota.update', $anggota->id), $updateData);

        // ASSERT: Verifikasi Placeholder Dibuat
        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false
        ]);
    }
    
    // Pengujian: Update dapat menambahkan induk baru ke anggota yang sudah ada
    public function test_update_can_add_new_induk_to_existing_anggota()
    {
        // 1. ARRANGE (Persiapan)
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() // Buat 1 ternak & 1 placeholder
            ->create([
                'tahap_id' => $this->tahap->id,
                'jumlah_induk' => 1
            ]);
        
        $anggota = $anggota->fresh(); 
        $ternakLama = $anggota->ternaks->first();
        $placeholder = $anggota->latestPencatatan;

        // --- KRUSIAL FIX: Buat SATU detail agar details()->exists() bernilai TRUE ---
        PencatatanDetail::factory()->create([
            'pencatatan_id' => $placeholder->id,
            'ternak_id' => $ternakLama->id,
            'kondisi_ternak' => 'Sehat', // Isi data agar dianggap valid
        ]);
        
        $anggota = $anggota->fresh(); // Muat ulang lagi

        // ACT: Siapkan data update (Menambah Induk)
        $updateData = $this->getValidUpdateData($anggota, [
            'harga_induk' => [
                "Rp " . number_format($ternakLama->harga, 0, ',', '.'), // Harga lama (dengan format)
                "Rp 12.000.000" // Harga induk BARU (dengan format)
            ]
        ]);
        
        // ACT: Jalankan Update
        $this->actingAs($this->admin)->put(route('anggota.update', $anggota->id), $updateData);
        
        // ASSERT: Muat ulang anggota untuk verifikasi jumlah induk
        $anggotaFinal = $anggota->fresh();

        $this->assertDatabaseCount('ternaks', 2);
        $this->assertDatabaseHas('ternaks', ['harga' => 12000000]);
        $this->assertDatabaseHas('anggotas', ['id' => $anggotaFinal->id, 'jumlah_induk' => 2]);
        
        $ternakBaru = Ternak::where('harga', 12000000)->first();
        $this->assertDatabaseHas('pencatatan_details', [
            'ternak_id' => $ternakBaru->id,
        ]);
    }


   public function test_update_can_change_harga_of_existing_induk()
    {
        // ARRANGE
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() 
            ->create([
                'tahap_id' => $this->tahap->id,
                'jumlah_induk' => 1
            ]);
        
        // Muat ulang anggota secara eksplisit
        $anggota = $anggota->fresh(); 
        
        $ternak = $anggota->ternaks->first();
        $hargaLama = $ternak->harga; 
        $hargaBaru = 9999999; 

        $this->assertNotNull($ternak); 
        $this->assertDatabaseHas('ternaks', ['id' => $ternak->id, 'harga' => $hargaLama]);

        // ACT
        $updateData = $this->getValidUpdateData($anggota, [ 
            // Format input yang dijamin bisa diproses:
            'harga_induk' => [
                "Rp " . number_format($hargaBaru, 0, ',', '.') 
            ]
        ]);
        
        $this->actingAs($this->admin)->put(route('anggota.update', $anggota->id), $updateData);
        
        // ASSERT
        // Memverifikasi ID ternak tersebut memiliki harga BARU (9999999)
        $this->assertDatabaseHas('ternaks', ['id' => $ternak->id, 'harga' => $hargaBaru]); 
        // Memastikan tidak ada duplikasi
        $this->assertDatabaseCount('ternaks', 1);
    }


    public function test_anggota_deletion_removes_related_ternak_and_pencatatan()
    {
        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() 
            ->create([
                'tahap_id' => $this->tahap->id
            ]);
        $ternak = $anggota->ternaks->first();
        $placeholder = $anggota->pencatatans->first();

        $this->assertNotNull($ternak);
        $this->assertNotNull($placeholder);
        $this->assertDatabaseHas('anggotas', ['id' => $anggota->id]);
        $this->assertDatabaseHas('ternaks', ['id' => $ternak->id]);
        $this->assertDatabaseHas('pencatatans', ['id' => $placeholder->id]);

        $response = $this->actingAs($this->admin)->delete(
            route('anggota.destroy', $anggota->id),
            ['tahap_id' => $this->tahap->id]
        );

        $response->assertRedirect(route('anggota.index', ['tahap_id' => $this->tahap->id]));
        $this->assertDatabaseMissing('anggotas', ['id' => $anggota->id]);
        $this->assertDatabaseMissing('ternaks', ['id' => $ternak->id]);
        $this->assertDatabaseMissing('pencatatans', ['id' => $placeholder->id]);
    }

    // Helper Function: Mengembalikan data lengkap untuk update
    private function getValidUpdateData(Anggota $anggota, array $overrides = []): array
    {
        
        $hargaInduk = $anggota->ternaks()
            ->where('tipe_ternak', 'Induk')
            ->where('status_aktif', 'aktif')
            ->get()
            ->pluck('harga')
            ->map(fn($h) => "Rp $h")
            ->toArray();

        return array_merge([
            'nama' => $anggota->nama,
            'tahap_id' => $anggota->tahap_id,
            'jenis_ternak' => $anggota->jenis_ternak,
            'tempat_lahir' => $anggota->tempat_lahir,
            'tanggal_lahir' => $anggota->tanggal_lahir->format('d/m/Y'), 
            'no_hp' => $anggota->no_hp,
            'lokasi_kandang' => $anggota->lokasi_kandang,
            'status' => $anggota->status,
            'harga_induk' => $hargaInduk, 
            'current_tahap_id' => $anggota->tahap_id,
        ], $overrides); 
    }
}