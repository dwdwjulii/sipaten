<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Tahap;

class TahapTest extends TestCase
{
    use RefreshDatabase;

    // pengujian validasi format label tahap valid
    /** @test */
    public function label_format_is_correct()
    {
        $tahap = Tahap::factory()->create([
            'tahap_ke' => 'Tahap 1',
            'tahun' => 2025
        ]);

        $label = $tahap->label;

        $this->assertEquals("Tahap 1 (2025)", $label);
    }
}