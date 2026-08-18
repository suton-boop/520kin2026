<?php

namespace App\Exports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReportsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $activities;

    public function __construct($activities)
    {
        $this->activities = $activities;
    }

    public function collection()
    {
        return $this->activities;
    }

    public function headings(): array
    {
        return [
            'No',
            'Divisi / GM',
            'Nama Proyek (Induk)',
            'Nama Kegiatan / Task',
            'Deskripsi',
            'Periode',
            'Jadwal Rencana (Start)',
            'Jadwal Rencana (End)',
            'Tanggal Realisasi Selesai',
            'Ceklis Laporan (Status)',
            'Status Kegiatan',
            'Kendala / Akar Masalah',
            'Rencana Mitigasi / Solusi'
        ];
    }

    public function map($activity): array
    {
        static $row = 0;
        $row++;

        $gmName = 'Umum';
        if ($activity->reportSubmission && $activity->reportSubmission->user && $activity->reportSubmission->user->gugusMutu) {
            $gmName = $activity->reportSubmission->user->gugusMutu->name;
        }

        $projectName = '-';
        if ($activity->reportSubmission && $activity->reportSubmission->project) {
            $projectName = $activity->reportSubmission->project->name;
        }

        $periodName = '-';
        if ($activity->reportSubmission && $activity->reportSubmission->period) {
            $periodName = $activity->reportSubmission->period->month_year;
        }
        
        $approvalStatus = '-';
        if ($activity->reportSubmission) {
            $approvalStatus = $activity->reportSubmission->approval_status;
        }

        return [
            $row,
            $gmName,
            $projectName,
            $activity->nama_kegiatan_turunan,
            $activity->deskripsi_kegiatan,
            $periodName,
            $activity->rencana_start_date,
            $activity->rencana_end_date,
            $activity->realisasi_end_date,
            $approvalStatus,
            $activity->status_akhir,
            $activity->kendala,
            $activity->mitigasi
        ];
    }
}
