<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anggaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'urut',
        'kode',
        'tipe',
        'nomenklatur',
        'satuan',
        'volume',
        'volume_realisasi',
        'pelaksanaan',
        'anggaran_alokasi',
        'anggaran_realisasi',
        'kelengkapan',
        'is_active',
    ];

    protected $casts = [
        'pelaksanaan' => 'decimal:2',
        'kelengkapan' => 'array',
        'is_active' => 'boolean',
    ];

    // Get children
    public function children()
    {
        return $this->hasMany(Anggaran::class, 'parent_id');
    }

    // Get parent
    public function parent()
    {
        return $this->belongsTo(Anggaran::class, 'parent_id');
    }
}
