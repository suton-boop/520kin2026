<?php

namespace App\Observers;

use App\Models\ProjectTask;
use App\Models\Activity;
use App\Models\ReportSubmission;

class ProjectTaskObserver
{
    public function created(ProjectTask $projectTask): void
    {
        $this->syncToReports($projectTask);
    }

    public function updated(ProjectTask $projectTask): void
    {
        $this->syncToReports($projectTask);
    }

    public function deleted(ProjectTask $projectTask): void
    {
        Activity::where('project_task_id', $projectTask->id)
            ->whereHas('reportSubmission', function ($q) {
                $q->whereIn('approval_status', ['Draft', 'Pending']);
            })->delete();
    }

    private function syncToReports(ProjectTask $projectTask): void
    {
        if (!$projectTask->start_date) return;

        // Auto-create Period for the task's start month
        $date = \Carbon\Carbon::parse($projectTask->start_date);
        $monthYear = $date->locale('id')->isoFormat('MMMM YYYY');
        
        $period = \App\Models\Period::firstOrCreate(
            ['month_year' => $monthYear], 
            ['start_date' => $date->copy()->startOfMonth()->toDateString(), 'end_date' => $date->copy()->endOfMonth()->toDateString()]
        );

        $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;

        $report = ReportSubmission::firstOrCreate([
            'project_id' => $projectTask->project_id,
            'period_id' => $period->id,
        ], [
            'user_id' => $userId,
            'form_type' => 'Laporan', 
            'approval_status' => 'Draft'
        ]);

        Activity::updateOrCreate(
            [
                'report_submission_id' => $report->id,
                'project_task_id' => $projectTask->id,
            ],
            [
                'nama_kegiatan_turunan' => $projectTask->name,
                'rencana_start_date' => $projectTask->start_date,
                'rencana_end_date' => $projectTask->finish_date,
                'percent_complete' => $projectTask->percent_complete,
                'duration_days' => $projectTask->duration_days,
            ]
        );
    }
}

