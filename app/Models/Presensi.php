<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Presensi extends Model
{
    protected $fillable = [
        'tanggal',
        'kelas_id',
        'jenis',
        'mapel_id',
        'dicatat_oleh',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mapel_id');
    }

    public function pencatat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    // Relasi Asli
    public function presensiJams(): HasMany
    {
        return $this->hasMany(PresensiJam::class);
    }

    public function presensiDetails(): HasMany
    {
        return $this->hasMany(PresensiDetail::class);
    }

    // Alias Relasi (Agar aman dipanggil dari mana saja)
    public function guru(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dicatat_oleh');
    }

    public function jams(): HasMany
    {
        return $this->hasMany(PresensiJam::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(PresensiDetail::class);
    }
}