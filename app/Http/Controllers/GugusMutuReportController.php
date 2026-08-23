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

        $gugusMutus = GugusMutu::with(['users.reportSubmissions' => function ($query) use ($periodId) {
            if ($periodId && $periodId !== 'all') {
                $query->where('period_id', $periodId);
            }
            $query->withCount('activities')
                  ->withCount(['activities as activities_completed_count' => function($q) { $q->where('status_akhir', 'Selesai')->orWhere('percent_complete', '>=', 100); }]);
        }])->get();

        $reportData = $gugusMutus->map(function ($gugus) {
            $totalTarget = 0;
            $totalCapaian = 0;
            
            foreach ($gugus->users as $user) {
                foreach ($user->reportSubmissions as $submission) {
                    $totalTarget += $submission->activities_count ?? 0;
                    $totalCapaian += $submission->activities_completed_count ?? 0;
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
