<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\Activity;
use App\Models\ReportSubmission;
use App\Models\Period;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

// Disable events temporarily
ProjectTask::flushEventListeners();

// Clean up existing tasks and reports to start fresh
Activity::query()->delete();
ProjectTask::query()->delete();
ReportSubmission::query()->delete();

$project1 = Project::find(1);
$project2 = Project::find(2);
$admin = User::first(); // Or get staff user

if(!$project1 || !$project2) {
    echo "Projects not found. Exiting.\n";
    exit(1);
}

$months = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus'
];

$taskCount = 1;
foreach ($months as $mNum => $mName) {
    $monthYearStr = $mName . ' 2026';
    $period = Period::firstOrCreate(
        ['month_year' => $monthYearStr],
        [
            'start_date' => Carbon::create(2026, $mNum, 1)->toDateString(),
            'end_date' => Carbon::create(2026, $mNum, 1)->endOfMonth()->toDateString(),
            'is_active' => ($mNum == 8) // Only August is active
        ]
    );

    // Create Report Submissions
    $status = ($mNum == 8) ? 'Draft' : 'Approved';

    $rep1 = ReportSubmission::create([
        'user_id' => $admin->id,
        'period_id' => $period->id,
        'project_id' => $project1->id,
        'form_type' => 'Laporan',
        'approval_status' => $status
    ]);

    $rep2 = ReportSubmission::create([
        'user_id' => $admin->id,
        'period_id' => $period->id,
        'project_id' => $project2->id,
        'form_type' => 'Laporan',
        'approval_status' => $status
    ]);

    // Create 3 tasks per month per project
    for ($i = 1; $i <= 3; $i++) {
        $start = Carbon::create(2026, $mNum, 1)->addDays(rand(1, 10));
        $finish = Carbon::create(2026, $mNum, 1)->addDays(rand(15, 25));
        
        // Task for Project 1
        $pt1 = ProjectTask::create([
            'project_id' => $project1->id,
            'name' => "Task P1 M$mNum-$i",
            'start_date' => $start->toDateString(),
            'finish_date' => $finish->toDateString(),
            'percent_complete' => 100,
            'duration_days' => $start->diffInDays($finish),
            'wbs_code' => "1.$mNum.$i"
        ]);
        
        // Activity for Project 1
        Activity::create([
            'report_submission_id' => $rep1->id,
            'project_task_id' => $pt1->id,
            'nama_kegiatan_turunan' => $pt1->name,
            'rencana_start_date' => $pt1->start_date,
            'rencana_end_date' => $pt1->finish_date,
            'realisasi_start_date' => $pt1->start_date,
            'realisasi_end_date' => ($mNum == 8 && $i == 3) ? null : $pt1->finish_date, // Leave one unfinished in August
            'percent_complete' => ($mNum == 8 && $i == 3) ? 50 : 100,
            'status_akhir' => ($mNum == 8 && $i == 3) ? 'Proses' : 'Selesai',
        ]);

        // Task for Project 2
        $pt2 = ProjectTask::create([
            'project_id' => $project2->id,
            'name' => "Task P2 M$mNum-$i",
            'start_date' => $start->toDateString(),
            'finish_date' => $finish->toDateString(),
            'percent_complete' => 100,
            'duration_days' => $start->diffInDays($finish),
            'wbs_code' => "2.$mNum.$i"
        ]);
        
        // Activity for Project 2
        Activity::create([
            'report_submission_id' => $rep2->id,
            'project_task_id' => $pt2->id,
            'nama_kegiatan_turunan' => $pt2->name,
            'rencana_start_date' => $pt2->start_date,
            'rencana_end_date' => $pt2->finish_date,
            'realisasi_start_date' => $pt2->start_date,
            'realisasi_end_date' => ($mNum == 8 && $i == 3) ? null : $pt2->finish_date,
            'percent_complete' => ($mNum == 8 && $i == 3) ? 50 : 100,
            'status_akhir' => ($mNum == 8 && $i == 3) ? 'Proses' : 'Selesai',
        ]);
    }
}

// Ensure the rest of the year (Sep-Dec) has planned tasks but NO realisasi yet (to form the S-curve target)
foreach ([9, 10, 11, 12] as $mNum) {
    for ($i = 1; $i <= 3; $i++) {
        $start = Carbon::create(2026, $mNum, 1)->addDays(rand(1, 10));
        $finish = Carbon::create(2026, $mNum, 1)->addDays(rand(15, 25));
        
        ProjectTask::create([
            'project_id' => $project1->id,
            'name' => "Task P1 M$mNum-$i",
            'start_date' => $start->toDateString(),
            'finish_date' => $finish->toDateString(),
            'percent_complete' => 0,
            'duration_days' => $start->diffInDays($finish),
            'wbs_code' => "1.$mNum.$i"
        ]);
        
        ProjectTask::create([
            'project_id' => $project2->id,
            'name' => "Task P2 M$mNum-$i",
            'start_date' => $start->toDateString(),
            'finish_date' => $finish->toDateString(),
            'percent_complete' => 0,
            'duration_days' => $start->diffInDays($finish),
            'wbs_code' => "2.$mNum.$i"
        ]);
    }
}

echo "Jan-Aug data seeded beautifully.\n";
