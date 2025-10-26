<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Arsip extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'diarsipkan_oleh',
        'nama_file',
        'path_file',
        'bulan',
        'tahun',

    ];

    /**
     * Relasi ke User: Satu Arsip dibuat oleh (belongsTo) satu User (Admin).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diarsipkan_oleh');
    }
}
