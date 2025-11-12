<?php

namespace Tests\Feature;
use App\Models\Anggota;
use App\Models\Arsip;
use App\Models\Pencatatan;
use App\Models\Tahap;
use App\Models\Ternak;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class AnggotaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Tahap $tahap;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow(Carbon::parse('2025-01-01 10:00:00'));

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->tahap = Tahap::factory()->create(['tahun' => 2025, 'tahap_ke' => 1]); 
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow(null);
        parent::tearDown();
    }

    // Memastikan Admin berhasil membuat anggota aktif dan placeholder pencatatan
    /** @test */
    public function store_anggota_aktif_creates_active_placeholder()
    {
        $anggotaData = $this->getValidStoreData(['status' => 'aktif', 'nama' => 'Anggota Aktif']);
        
        $response = $this->actingAs($this->admin)->post(route('anggota.store'), $anggotaData);
        $response->assertSessionHas('success');

        $anggotaBaru = Anggota::where('nama', 'Anggota Aktif')->first();
        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggotaBaru->id,
            'is_locked' => false,
            'tanggal_catatan' => Carbon::now() 
        ]);
        $this->assertDatabaseCount('ternaks', 2);
    }

    // Memastikan placeholder tidak dibuat jika status non-aktif
    /** @test */
    public function store_anggota_non_aktif_does_not_create_placeholder()
    {
        $anggotaDataNonAktif = $this->getValidStoreData(['status' => 'non-aktif', 'nama' => 'Anggota Non-Aktif']);
        
        $this->actingAs($this->admin)->post(route('anggota.store'), $anggotaDataNonAktif);
        
        $anggota1 = Anggota::where('nama', 'Anggota Non-Aktif')->first();

        $this->assertDatabaseMissing('pencatatans', ['anggota_id' => $anggota1->id]);
    }
    
    // Memastikan placeholder tidak dibuat jika periode sudah diarsip.
    /** @test */
    public function store_anggota_if_period_is_archived_does_not_create_placeholder()
    {
 
        Arsip::factory()->create(['bulan' => Carbon::now()->month, 'tahun' => Carbon::now()->year]);
        
        $anggotaDataArsip = $this->getValidStoreData(['status' => 'aktif', 'nama' => 'Anggota Arsip']);
        
        $this->actingAs($this->admin)->post(route('anggota.store'), $anggotaDataArsip);
        
        $anggota2 = Anggota::where('nama', 'Anggota Arsip')->first();

        $this->assertDatabaseMissing('pencatatans', ['anggota_id' => $anggota2->id]);
    }
    
    // Memastikan placeholder dihapus ketika status anggota diubah dari aktif menjadi non-aktif.
    /** @test */
    public function update_status_aktif_to_non_aktif_deletes_placeholder()
    {

        $anggota = Anggota::factory()
            ->withPencatatanPlaceholder()
            ->create([
                'tahap_id' => $this->tahap->id,
                'status' => 'aktif',
            ]);

        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false,
        ]);

        $updateData = $this->getValidUpdateData($anggota, ['status' => 'non-aktif']);

        $this->actingAs($this->admin)->put(route('anggota.update', $anggota->id), $updateData);

        $anggota->refresh();

        $this->assertDatabaseMissing('pencatatans', [
            'anggota_id' => $anggota->id,
            'is_locked' => false,
        ]);
    }


    // Memastikan placeholder baru dibuat ketika status anggota diubah dari non-aktif menjadi aktif.
    /** @test */
    public function update_status_non_aktif_to_aktif_creates_placeholder()
    {
        Pencatatan::factory()->create(['tanggal_catatan' => Carbon::now()->startOfDay(), 'is_locked' => false]);

        $anggota = Anggota::factory()->nonaktif()->create(['tahap_id' => $this->tahap->id]);
        Ternak::factory()->create(['anggota_id' => $anggota->id, 'tipe_ternak' => 'Induk', 'status_aktif' => 'aktif', 'harga' => 10000000]);

        $this->assertDatabaseMissing('pencatatans', ['anggota_id' => $anggota->id]);

        $updateData = $this->getValidUpdateData($anggota->fresh(), ['status' => 'aktif']);

        $this->actingAs($this->admin)->put(route('anggota.update', $anggota->id), $updateData);

        $this->assertDatabaseHas('pencatatans', [
            'anggota_id' => $anggota->id, 
            'is_locked' => false,
            'tanggal_catatan' => Carbon::now()
        ]);
    }
    
    // Memastikan penghapusan anggota juga menghapus semua data relasinya (ternak dan pencatatan).
    /** @test */
    public function test_anggota_deletion_removes_related_data()
    {
        $anggota = Anggota::factory()->withPencatatanPlaceholder()->create(['tahap_id' => $this->tahap->id]);
        $ternak = $anggota->ternaks->first();
        $placeholder = $anggota->pencatatans->first();

        $response = $this->actingAs($this->admin)->delete(
            route('anggota.destroy', $anggota->id),
            ['tahap_id' => $this->tahap->id]
        );

        $response->assertRedirect();
        $this->assertDatabaseMissing('anggotas', ['id' => $anggota->id]);
        $this->assertDatabaseMissing('ternaks', ['id' => $ternak->id]);
        $this->assertDatabaseMissing('pencatatans', ['id' => $placeholder->id]);
    }

    private function getValidStoreData(array $overrides = []): array
    {
        return array_merge([
            'tahap_id' => $this->tahap->id,
            'nama' => 'Anggota Uji',
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
        ], $overrides);
    }
    
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