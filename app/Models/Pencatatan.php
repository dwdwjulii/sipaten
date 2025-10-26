<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Pencatatan extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'anggota_id',
        'petugas_id',
        'tanggal_catatan',
        'temuan_lapangan',
        'foto_dokumentasi',
        'is_locked',
    ];

    protected $casts = [
        'foto_dokumentasi' => 'array',
        'tanggal_catatan' => 'date',
        'is_locked' => 'boolean',
    ];

    

    // Relasi ke detail-detail ternak yang dicatat saat kunjungan ini
    public function details(): HasMany
    {
        return $this->hasMany(PencatatanDetail::class);
    }

    // Relasi ke Anggota (Setiap pencatatan milik 1 anggota)
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    // Relasi ke User (Setiap pencatatan dibuat oleh 1 petugas)
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
    
    // Relasi ke Ternak (Setiap pencatatan memiliki banyak data ternak)
    public function ternaks(): BelongsToMany
    {
        return $this->belongsToMany(Ternak::class, 'pencatatan_details', 'pencatatan_id', 'ternak_id');
    }

    // Helper untuk cek status arsip
    public function isLocked(): bool
    {
        return $this->is_locked;
    }
}