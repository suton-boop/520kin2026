<?php

namespace App\Http\Controllers;

use App\Models\GugusMutu;
use App\Models\Period;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GugusMutuReportController extends Controller
{
    public function index(Request $request)
    {
        $periodId = $request->input('period_id');
        if ($periodId === null) {
            $periodId = Period::orderBy('id', 'desc')->first()?->id;
        }

        $periods = Period::orderBy('id', 'desc')->get();

        $gugusMutus = GugusMutu::all();

        // Get all activities for the selected period
        $activitiesQuery = \App\Models\Activity::with(['projectTask.project', 'reportSubmission.user']);
        
        if ($periodId && $periodId !== 'all') {
            $activitiesQuery->whereHas('reportSubmission', function($q) use ($periodId) {
                $q->where('period_id', $periodId);
            });
        }
        
        $activities = $activitiesQuery->get();

        $reportData = $gugusMutus->map(function ($gugus) use ($activities) {
            $totalTarget = 0;
            $totalCapaian = 0;
            
            foreach ($activities as $act) {
                $actGmId = null;
                if ($act->projectTask && $act->projectTask->project) {
                    $actGmId = $act->projectTask->project->gugus_mutu_id;
                } elseif ($act->reportSubmission && $act->reportSubmission->user) {
                    $actGmId = $act->reportSubmission->user->gugus_mutu_id;
                }
                
                if ($actGmId == $gugus->id) {
                    $totalTarget++;
                    if ($act->status_akhir === 'Selesai' || $act->percent_complete >= 100) {
                        $totalCapaian++;
                    }
                }
            }

            return [
                'id' => $gugus->id,
                'name' => $gugus->name,
                'total_target' => $totalTarget,
                'total_capaian' => $totalCapaian,
                'achievement' => $totalTarget > 0 ? round(($totalCapaian / $totalTarget) * 100, 2) : 0,
            ];
        });

        return Inertia::render('GugusMutuReport/Index', [
            'reportData' => $reportData,
            'periods' => $periods,
            'selectedPeriodId' => $periodId,
        ]);
    }
}
