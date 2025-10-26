<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PencatatanDetail extends Model
{
    use HasFactory, HasUlids;

    protected $fillable = [
        'pencatatan_id',
        'ternak_id',
        'umur_saat_dicatat',
        'kondisi_ternak',
        'status_vaksin',
    ];

    public function ternak(): BelongsTo
    {
        return $this->belongsTo(Ternak::class);
    }
}