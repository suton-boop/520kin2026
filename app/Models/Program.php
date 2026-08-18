<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'satuan',
        'target',
        'alokasi',
    ];

    protected $appends = [
        'realisasi_target',
        'realisasi_alokasi',
    ];

    public function kegiatans()
    {
        return $this->hasMany(Kegiatan::class);
    }

    public function getRealisasiTargetAttribute()
    {
        return $this->kegiatans()->sum('volume_realisasi');
    }

    public function getRealisasiAlokasiAttribute()
    {
        return $this->kegiatans()->sum('anggaran_realisasi');
    }
}