<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AnggaranTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'RO 7605.QDB.750', 
                'Satuan PAUD, Dikdas, Dikmen dan Dikmas yang difasilitasi penjaminan mutunya', 
                '', 
                '5725', 
                '0', 
                '4310265000', 
                '3235455127', 
                '75.1'
            ],
            [
                '1 KOMP 091', 
                'Pelaksanaan Pembinaan Kurikulum Merdeka', 
                'Sekolah', 
                '7', 
                '0', 
                '47180000', 
                '0', 
                '100'
            ],
            [
                '2 KOMP 092', 
                'Pelaksanaan Pembinaan Asesmen Nasional', 
                'Sekolah', 
                '16', 
                '0', 
                '47376000', 
                '0', 
                '100'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'kode',
            'nomenklatur',
            'satuan',
            'volume',
            'volume_realisasi',
            'alokasi',
            'realisasi',
            'pelaksanaan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1    => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']]],
            2    => ['font' => ['bold' => true, 'color' => ['rgb' => '1E3A8A']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF3C7']]],
        ];
    }
}
