<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ternak extends Model
{
    use HasFactory, HasUlids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Atribut yang diizinkan untuk diisi secara massal (mass assignable).
     */
    protected $fillable = [
        'anggota_id',
        'induk_id',
        'tipe_ternak',
        'no_ear_tag',
        'jenis_kelamin',
        'harga',
        'status_aktif',
    ];

    /**
     * Mendapatkan data induk dari seekor anak ternak.
     * (Seekor Anak 'milik' satu Induk)
     */
    public function induk(): BelongsTo
    {
        return $this->belongsTo(Ternak::class, 'induk_id');
    }

    /**
     * Mendapatkan semua anak dari seekor induk ternak.
     * (Seekor Induk 'memiliki banyak' Anak)
     */
    public function anak(): HasMany
    {
        return $this->hasMany(Ternak::class, 'induk_id');
    }

    /**
     * Relasi ke Anggota: Satu Ternak dimiliki oleh (belongsTo) satu Anggota.
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class);
    }

    /**
     * Relasi ke Detail Pencatatan: Satu Ternak (data master) bisa memiliki banyak 
     * record kondisi historis (data transaksi).
     */
    public function pencatatanDetails(): HasMany
    {
        return $this->hasMany(PencatatanDetail::class);
    }
}