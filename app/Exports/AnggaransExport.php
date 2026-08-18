<?php

namespace App\Exports;

use App\Models\Anggaran;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AnggaransExport implements FromCollection, WithHeadings, WithMapping
{
    protected $anggarans;

    public function __construct($anggarans)
    {
        $this->anggarans = $anggarans;
    }

    public function collection()
    {
        return $this->anggarans;
    }

    public function headings(): array
    {
        return [
            'No',
            'Kode PMO',
            'Kode RRKL',
            'Anggaran Alokasi (Rp)',
            'Anggaran Realisasi (Rp)',
            'Tahun',
            'Status'
        ];
    }

    public function map($anggaran): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            $anggaran->kode_pmo,
            $anggaran->kode_rrkl,
            $anggaran->anggaran_alokasi,
            $anggaran->anggaran_realisasi,
            $anggaran->tahun,
            $anggaran->is_active ? 'Aktif' : 'Non-Aktif'
        ];
    }
}
