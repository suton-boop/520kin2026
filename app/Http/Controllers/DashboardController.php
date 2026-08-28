<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Activity;
use App\Models\ReportSubmission;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $year = $request->input('year', 2026);
        $month = $request->input('month');
        $gugusMutuId = $request->input('gugus_mutu_id');
        return $this->getDashboardData($request, $user, $year, $month, $gugusMutuId, 'Dashboard');
    }

    public function publicDashboard(Request $request)
    {
        $year = $request->input('year', 2026);
        $month = $request->input('month');
        $gugusMutuId = $request->input('gugus_mutu_id');
        return $this->getDashboardData($request, null, $year, $month, $gugusMutuId, 'Welcome');
    }

    private function getDashboardData(Request $request, $user, $year, $month, $gugusMutuId, $component)
    {
        $today = Carbon::now();

        $reportQuery = ReportSubmission::with(['user.gugusMutu', 'period'])
            ->whereHas('period', function ($q) use ($year, $month) {
                if ($month) {
                    $monthPadded = str_pad($month, 2, '0', STR_PAD_LEFT);
                    $q->where('month_year', "{$monthPadded}-{$year}");
                } else {
                    $q->where('month_year', 'like', "%{$year}%");
                }
            });

        if ($user) {
            if ($user->hasRole(['manager', 'staff', 'user']) && $user->gugus_mutu_id) {
                $reportQuery->whereHas('user', function($q) use ($user) {
                    $q->where('gugus_mutu_id', $user->gugus_mutu_id);
                });
            }
        }



        $submissions = $reportQuery->get();
        $submissionIds = $submissions->pluck('id');

        $allActivities = Activity::with(['budget', 'reportSubmission.user.gugusMutu', 'reportSubmission.period', 'projectTask.project.gugusMutu'])->whereIn('report_submission_id', $submissionIds)
            ->orderBy('kode_pmo', 'asc')
            ->get();

        if ($gugusMutuId) {
            $allActivities = $allActivities->filter(function($act) use ($gugusMutuId) {
                 $actGmId = null;
                 if ($act->projectTask && $act->projectTask->project) {
                     $actGmId = $act->projectTask->project->gugus_mutu_id;
                 } elseif ($act->reportSubmission && $act->reportSubmission->user) {
                     $actGmId = $act->reportSubmission->user->gugus_mutu_id;
                 }
                 return $actGmId == $gugusMutuId;
            })->values();
        }

        // Calculate Late Tasks
        $lateTasks = $allActivities->filter(function($act) use ($today) {
            if (empty($act->rencana_end_date)) return false;
            $endDate = Carbon::parse($act->rencana_end_date);
            return $endDate->isPast() && empty($act->realisasi_end_date) && !in_array($act->status_akhir, ['Selesai', 'Sudah']);
        })->values();

        // Calculate Invalid Budget (Ang Invalid)
        $invalidBudgets = $allActivities->filter(function($act) {
            $budget = $act->budget;
            if (!$budget) return true;
            $alokasi = floatval($budget->anggaran_alokasi);
            $realisasi = floatval($budget->anggaran_realisasi);
            return ($realisasi > $alokasi) || ($alokasi <= 0);
        })->values();

        // Monthly Performance Stats (Cumulative: Target from Perencanaan, Realisasi from Pelaporan)
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyStats = [];
        
        // Target: Ambil murni dari Master Perencanaan (ProjectTask)
        $projectTaskQuery = \App\Models\ProjectTask::whereHas('project', function($q) use ($year) {
            // Optional: filter project active year if needed
        })->where(function($q) use ($year) {
            $q->whereYear('finish_date', $year)->orWhereYear('start_date', $year);
        });

        if ($user && $user->hasRole(['manager', 'staff', 'user']) && $user->gugus_mutu_id && !($user->hasRole('manager') && empty($user->gugus_mutu_id))) {
            $projectTaskQuery->whereHas('project', function($q) use ($user) {
                $q->where('gugus_mutu_id', $user->gugus_mutu_id);
            });
        }
        
        $allProjectTasks = $projectTaskQuery->get();
        $totalYearProjectTasks = $allProjectTasks->count() > 0 ? $allProjectTasks->count() : 1;

        foreach ($months as $index => $m) {
            $monthNum = $index + 1;
            $monthDate = Carbon::create($year, $monthNum, 1)->endOfMonth();
            
            $cumulativePlanned = $allProjectTasks->filter(function($task) use ($monthDate) {
                 return !empty($task->finish_date) && Carbon::parse($task->finish_date) <= $monthDate;
            })->count();

            $cumulativeFinished = $allActivities->filter(function($act) use ($monthDate) {
                return !empty($act->realisasi_end_date) && Carbon::parse($act->realisasi_end_date) <= $monthDate;
            })->count();

            $target = round(($cumulativePlanned / $totalYearProjectTasks) * 100);
            $realisasi = round(($cumulativeFinished / $totalYearProjectTasks) * 100);

            $monthlyStats[] = [
                'name' => $m,
                'target' => $target,
                'realisasi' => $realisasi,
            ];
        }

        // Monthly Budget Stats (Detailed)
        $budgetStats = [];
        $totalAlokasiGlobal = \App\Models\Anggaran::sum('anggaran_alokasi');
        $totalRealisasiGlobal = \App\Models\Anggaran::sum('anggaran_realisasi');
        
        // CUMULATIVE calculation
        $cumulativeReal = 0;
        
        foreach ($months as $index => $m) {
            $monthNum = $index + 1;
            
            // Alokasi is still linearly distributed for target curve
            $monthTarget = $totalAlokasiGlobal > 0 ? round(($totalAlokasiGlobal / 12) * $monthNum) : 0;
            
            // Realisasi comes from actual activities that are approved in this month (using realisasi_end_date)
            // But if realisasi_end_date is null, we might fallback to updated_at or not count it.
            // Let's sum for this specific month, then add to cumulative.
            $monthRealAdded = \App\Models\Activity::where('approval_status', 'Approved')
                ->whereYear('realisasi_end_date', $year)
                ->whereMonth('realisasi_end_date', $monthNum)
                ->sum('anggaran_realisasi');
                
            $cumulativeReal += $monthRealAdded;
            
            $budgetStats[] = [
                'name' => $m,
                'target' => $monthTarget,
                'realisasi' => $cumulativeReal,
                'persentase' => $monthTarget > 0 ? round(($cumulativeReal / $monthTarget) * 100) : 0
            ];
        }

        $divisions = \App\Models\GugusMutu::all();
        $periods = \App\Models\Period::all();

        return Inertia::render($component, [
            'divisions' => $divisions,
            'periods' => $periods,
            'selectedMonth' => $month,
            'selectedDivision' => $gugusMutuId,
            'metrics' => [
                'total_terkirim' => $submissions->whereIn('approval_status', ['Draft', 'Pending'])->count(),
                'total_disetujui' => $submissions->where('approval_status', 'Approved')->count(),
                'total_ditolak' => $submissions->where('approval_status', 'Rejected')->count(),
                'anggaran' => [
                    'total_alokasi' => $totalAlokasiGlobal,
                    'total_realisasi' => $totalRealisasiGlobal,
                    'persentase' => $totalAlokasiGlobal > 0 ? round(($totalRealisasiGlobal / $totalAlokasiGlobal) * 100, 2) : 0,
                ],
            ],
            'activities' => $allActivities,
            'lateTasks' => $lateTasks,
            'invalidBudgets' => $invalidBudgets,
            'monthlyStats' => $monthlyStats, 
            'budgetStats' => $budgetStats,
            'selectedYear' => $year,
            'canLogin' => true,
            'canRegister' => true             
        ]);
    }
}





