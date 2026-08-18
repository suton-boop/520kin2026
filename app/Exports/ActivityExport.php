<?php

namespace App\Exports;

use App\Models\ProjectTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityExport implements FromCollection, WithHeadings, WithMapping
{
    protected $projectId;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function collection()
    {
        return ProjectTask::where('project_id', $this->projectId)->get();
    }

    public function headings(): array
    {
        return [
            'Nama Kegiatan (Jangan Diubah)',
            'Rencana Start Date (YYYY-MM-DD)',
            'Rencana End Date (YYYY-MM-DD)',
            'Status Akhir (Belum / Proses / Selesai / Tunda)',
            'Deskripsi / Realisasi'
        ];
    }

    public function map($task): array
    {
        $endDate = null;
        if ($task->start_date) {
            $endDate = date('Y-m-d', strtotime($task->start_date . ' + ' . $task->duration_days . ' days'));
        }

        return [
            $task->name,
            $task->start_date,
            $endDate,
            'Belum',
            '' // Deskripsi dikosongkan untuk diisi
        ];
    }
}