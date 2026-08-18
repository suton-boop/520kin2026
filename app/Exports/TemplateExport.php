<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Illuminate\Support\Collection;

class TemplateExport implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Import Program';
    }

    public function headings(): array
    {
        return [
            'WBS', 
            'Task Name (Nama Kegiatan)', 
            'Indikator Kinerja', 
            'Hasil Kegiatan', 
            'Mekanisme', 
            '-', 
            'Peserta Sasaran', 
            'Tempat', 
            'Anggaran', 
            'Bulan (Rencana Mulai)', 
            'Bulan (Rencana Selesai)', 
            'Gugus Mutu'
        ];
    }

    public function collection()
    {
        return new Collection([
            [
                '1', 
                'PDM-01', 
                '', '', '', '', '', '', '', '', '', ''
            ],
            [
                '1.1', 
                '[PDM-01-1] 100% satuan pendidikan pelaksana PSP mengalami peningkatan kualitas transformasi satdik (a&b)', 
                '', '', '', '', '', '', '', '', '', ''
            ],
            [
                '1.1.1', 
                'Percepatan Transformasi Sekolah Pelaksana Program Sekolah Penggerak', 
                '', '', '', '', '', '', '', '', '', ''
            ],
            [
                '1.1.1.1', 
                'Koordinasi Percepatan Transformasi Satuan Pendidikan Sekolah Pelaksana PSP dengan PMO Daerah', 
                'Jumlah sekolah sasaran', 'Dokumen Laporan', 'Rapat', '', 'Pengawas', 'Dinas Pendidikan', '25000000', 'Februari', 'Maret', 'GM-01'
            ]
        ]);
    }
}