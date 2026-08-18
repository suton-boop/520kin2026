<?php

namespace App\Exports;

use App\Models\ProjectTask;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProjectsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tasks;

    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    public function collection()
    {
        return $this->tasks;
    }

    public function headings(): array
    {
        return [
            'No',
            'Divisi / GM',
            'Nama Proyek (Induk)',
            'WBS Code',
            'Nama Task / Kegiatan',
            'Tanggal Mulai (Rencana)',
            'Tanggal Selesai (Rencana)',
            'Durasi (Hari)',
            'Progress (%)'
        ];
    }

    public function map($task): array
    {
        static $row = 0;
        $row++;

        return [
            $row,
            ($task->project && $task->project->gugusMutu) ? $task->project->gugusMutu->name : 'Umum',
            $task->project ? $task->project->name : '-',
            $task->wbs_code,
            $task->name,
            $task->start_date,
            $task->finish_date,
            $task->duration_days,
            $task->percent_complete
        ];
    }
}
