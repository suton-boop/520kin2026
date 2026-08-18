<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_id',
        'nama',
        'volume_target',
        'volume_realisasi',
        'anggaran_alokasi',
        'anggaran_realisasi',
        'pelaksanaan',
        'kelengkapan_bulanan',
    ];

    protected $casts = [
        'kelengkapan_bulanan' => 'array',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}