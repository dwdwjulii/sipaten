<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Anggota extends Model
{
    use HasFactory, HasUlids;

    protected $table = 'anggotas';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * Kolom yang boleh diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'tahap_id',
        'nama',
        'jenis_ternak',
        'jumlah_induk',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'lokasi_kandang',
        'status',
    ];

    protected $appends = ['total_harga_induk'];

    
    protected $casts = [
        'tanggal_lahir' => 'date:Y-m-d',
        'jumlah_induk'  => 'integer',
    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    // --- RELASI DATABASE ---

    public function tahap(): BelongsTo
    {
        return $this->belongsTo(Tahap::class);
    }

    public function ternaks(): HasMany
    {
        return $this->hasMany(Ternak::class);
    }

    public function pencatatans(): HasMany
    {
        return $this->hasMany(Pencatatan::class);
    }

    // Relasi untuk pencatatan AKTIF periode ini (belum locked)
    public function latestPencatatan(): HasOne
    {
        return $this->hasOne(Pencatatan::class)
            ->where('is_locked', false)
            ->latestOfMany('tanggal_catatan');
    }


    /**
     * Total harga induk dihitung dari semua ternak bertipe 'Induk'.
     */

    public function getTotalHargaIndukAttribute(): float
    {
        return (float) $this->ternaks()
            ->where('tipe_ternak', 'Induk')
            ->where('status_aktif', 'aktif') 
            ->sum('harga');
    }
}
