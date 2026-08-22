<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProjectTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            ['Proyek Contoh A', 'Deskripsi singkat proyek A', 'GM5- UMUM'],
            ['Proyek Contoh B', 'Deskripsi singkat proyek B', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Proyek',
            'Deskripsi',
            'Devisi'
        ];
    }
}
