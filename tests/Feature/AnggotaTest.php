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
      /** @test */
    public function admin_can_create_new_anggota_aktif()
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
      /** @test */
    public function admin_create_anggota_non_aktif_does_not_create_placeholder()
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
      /** @test */
    public function admin_cannot_create_anggota_if_period_is_archived()
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
      /** @test */
    public function update_status_aktif_to_non_aktif_deletes_placeholder()
    {

        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder() 
            ->create([
                'tahap_id' => $this->tahap->id,
            ]);
        
        $anggota = $anggota->fresh(); 
        
        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false
        ]);

        $updateData = $this->getValidUpdateData($anggota, [ 
            'status' => 'non-aktif' 
        ]);

        $this->actingAs($this->admin)->put(route('anggota.update', $anggota->id), $updateData);

        $this->assertDatabaseMissing('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false
        ]);

        $this->assertDatabaseHas('anggotas', [
            'id' => $anggota->id,
            'status' => 'non-aktif' 
        ]);
    }

    // Pengujian: Update status non-aktif ke aktif membuat placeholder
      /** @test */
    public function update_status_non_aktif_to_aktif_creates_placeholder()
    {
        $anggota = Anggota::factory()
            ->nonaktif() 
            ->create([
                'tahap_id' => $this->tahap->id,
                'jumlah_induk' => 1
            ]);

        Ternak::factory()->create([
            'anggota_id' => $anggota->id,
            'tipe_ternak' => 'Induk',
            'status_aktif' => 'aktif',
            'harga' => 10000000 
        ]);
        
        $anggota = $anggota->fresh(); 
        $this->assertDatabaseMissing('pencatatans', ['anggota_id' => $anggota->id]);

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

        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false
        ]);
    }
    
      /** @test */
    public function anggota_deletion_removes_related_ternak_and_pencatatan()
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