<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tahap extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = ['tahun', 'tahap_ke'];

    // Satu tahap bisa memiliki BANYAK anggota
    public function anggotas()
    {
        return $this->hasMany(Anggota::class, 'tahap_id');
    }

    public function arsips(): HasMany
    {
        return $this->hasMany(Arsip::class);
    }

    // Accessor untuk membuat label otomatis, cth: "Tahap 1 (2025)"
    public function getLabelAttribute()
    {
        return "{$this->tahap_ke} ({$this->tahun})";
    }
}