<?php

namespace App\Imports;

use App\Models\Anggaran;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Str;

class AnggaranImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $rows
    */
    public function collection(Collection $rows)
    {
        $lastParentId = null;

        foreach ($rows as $row) {
            // Check if row has at least kode or nomenklatur
            if (empty($row['kode']) && empty($row['nomenklatur'])) {
                continue;
            }

            $kode = trim($row['kode'] ?? '');
            
            // Logic to determine WBS Parent (Level 1)
            // Typically RO (Rincian Output) is parent. We check if kode starts with "RO" or "tipe" is "parent".
            $isParent = Str::startsWith(strtoupper($kode), 'RO') || 
                        (isset($row['tipe']) && strtolower(trim($row['tipe'])) === 'parent');
            
            // If it's a parent, but we don't have a parent id yet, it will just be null.
            // If it's a child but we haven't seen a parent yet, it'll also be null (orphan child, but handled safely).
            
            $anggaran = Anggaran::create([
                'parent_id'          => $isParent ? null : $lastParentId,
                'kode'               => $kode,
                'nomenklatur'        => $row['nomenklatur'] ?? '-',
                'satuan'             => $row['satuan'] ?? null,
                'volume'             => $row['volume'] ?? null,
                'volume_realisasi'   => $row['volume_realisasi'] ?? null,
                'anggaran_alokasi'   => isset($row['alokasi']) ? (float) preg_replace('/[^0-9.]/', '', $row['alokasi']) : 0,
                'anggaran_realisasi' => isset($row['realisasi']) ? (float) preg_replace('/[^0-9.]/', '', $row['realisasi']) : 0,
                'pelaksanaan'        => isset($row['pelaksanaan']) ? (float) preg_replace('/[^0-9.]/', '', $row['pelaksanaan']) : 0,
                'kelengkapan'        => array_fill(0, 12, false),
                'is_active'          => 1,
            ]);

            if ($isParent) {
                $lastParentId = $anggaran->id;
            }
        }
    }
}
