<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiDetail extends Model
{
    protected $fillable = [
        'presensi_id',
        'siswa_id',
        'status',
        'keterangan',
    ];
    public $timestamps = true;
    public function presensi(): BelongsTo
    {
        return $this->belongsTo(Presensi::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}