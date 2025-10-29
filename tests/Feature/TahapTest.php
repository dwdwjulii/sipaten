<?php

namespace Tests\Feature;

// Impor model yang kita butuhkan
use App\Models\Anggota;
use App\Models\Tahap;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TahapTest extends TestCase
{

    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    
    // Memastikan logika pengecekan duplikat Anda berfungsi.
      /** @test */
    public function store_fails_if_tahap_already_exists()
    {
      
        Tahap::factory()->create([
            'tahun' => 2025,
            'tahap_ke' => 1,
        ]);
       
        $this->assertDatabaseCount('tahaps', 1);

        $tahapData = [
            'tahun' => 2025,
            'tahap_ke' => 1,
        ];
        
        $response = $this->actingAs($this->admin)->post(route('tahap.store'), $tahapData);
 
        $response->assertRedirect(route('anggota.index'));
        $response->assertSessionHas('error_tahap');
 
        $this->assertDatabaseCount('tahaps', 1);
    }

   
    // Memastikan delete Tahap berfungsi.
      /** @test */
    public function deleting_tahap_also_deletes_related_anggota()
    {

        $tahap = Tahap::factory()->create();
        $anggota = Anggota::factory(3)->create([
            'tahap_id' => $tahap->id
        ]);

        $this->assertDatabaseCount('tahaps', 1);
        $this->assertDatabaseCount('anggotas', 3);

        $response = $this->actingAs($this->admin)->delete(route('tahap.destroy', $tahap));

        $response->assertRedirect(route('anggota.index'));
        $response->assertSessionHas('deleted_tahap');

        $this->assertDatabaseMissing('tahaps', ['id' => $tahap->id]);
        $this->assertDatabaseMissing('anggotas', ['id' => $anggota[0]->id]);
        $this->assertDatabaseCount('anggotas', 0);
    }
}