<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ReportSubmission;
use App\Models\Project;
use App\Models\Activity;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActivityExport;
use App\Imports\ActivityImport;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
        public function index(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        
        // Base Query for Activities
        $query = Activity::with(['reportSubmission.period', 'reportSubmission.user.gugusMutu', 'reportSubmission.project.gugusMutu']);

        if ($user->hasRole(['admin', 'super-admin', 'superadmin']) || ($user->hasRole('manager') && empty($user->gugus_mutu_id))) {
            // Admin melihat semua kegiatan
        } elseif ($user->hasAnyRole(['manager', 'staff', 'user', 'operator'])) {
            if ($user->gugus_mutu_id && !($user->hasRole('manager') && empty($user->gugus_mutu_id))) {
                $query->whereHas('reportSubmission.user', function($q) use ($user) {
                    $q->where('gugus_mutu_id', $user->gugus_mutu_id);
                });
            } else {
                $query->whereHas('reportSubmission', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                });
            }
        }

        
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_kegiatan_turunan', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi_kegiatan', 'like', '%' . $request->search . '%');
            });
        }

                if ($request->gugus_mutu_id) {
            $query->whereHas('reportSubmission.user', function($q) use ($request) {
                if ($request->gugus_mutu_id == 5) {
                    $q->where('gugus_mutu_id', 5)->orWhereNull('gugus_mutu_id');
                } else {
                    $q->where('gugus_mutu_id', $request->gugus_mutu_id);
                }
            });
        }
        
        if ($request->status_akhir) {
            $query->where('status_akhir', $request->status_akhir);
        }

        $activities = $query->leftJoin('report_submissions', 'activities.report_submission_id', '=', 'report_submissions.id')
                            ->select('activities.*')
                            ->orderBy('report_submissions.created_at', 'desc')
                            ->orderBy('activities.created_at', 'desc')
                            ->paginate(15)->withQueryString();
        
        $allowImport = false;
        $isGlobalManager = $user->hasRole('manager') && empty($user->gugus_mutu_id);
        if ($user->hasRole(['admin', 'super-admin', 'superadmin']) || $isGlobalManager) {
            $allowImport = true;
        } elseif ($user->gugus_mutu_id) {
            $gm = \App\Models\GugusMutu::find($user->gugus_mutu_id);
            $allowImport = $gm ? (bool)$gm->allow_import : false;
        }

        
        $gugusMutus = \App\Models\GugusMutu::orderBy('name')->get();
        return Inertia::render('Reports/Index', [
            'activities' => $activities,
            'userRole' => $user->roles->pluck('name')->first(),
            'allowImport' => $allowImport,
            'gugusMutus' => $gugusMutus,
            'filters' => $request->only(['search', 'gugus_mutu_id', 'status_akhir']),
        ]);

    }

    public function store(Request $request)
    {
        $period = \App\Models\Period::firstOrCreate(
            ['month_year' => '01-2026'], 
            ['start_date' => '2026-01-01', 'end_date' => '2026-12-31']
        );
        $report = ReportSubmission::firstOrCreate([
            'user_id' => Auth::id(),
            'period_id' => $period->id,
        ], ['form_type' => 'plan', 'approval_status' => 'Draft']);
        return redirect()->route('reports.show', $report->id)->with('success', 'Rencana Pekerjaan Baru Berhasil Disiapkan.');
    }

    public function show($id)
    {
        $user = Auth::user();
        $report = ReportSubmission::with(['activities', 'period', 'user'])->findOrFail($id);
        
        $isAdmin = $user->hasRole(['admin', 'super-admin', 'superadmin']);
        $isOwner = $report->user_id === $user->id;
        $isSameGM = ($user->gugus_mutu_id && $report->user->gugus_mutu_id === $user->gugus_mutu_id);

        if (!$isAdmin && !$isOwner && !$isSameGM) {
            abort(403, 'Anda tidak memiliki akses ke laporan ini.');
        }

        // HITUNG IZIN EDIT DI SINI (SERVER-SIDE)
        $canEdit = $isAdmin || (
            ($isOwner || $isSameGM) && 
            ($report->approval_status === 'Draft' || str_contains($report->approval_status, 'Rejected'))
        );

        $allowImport = false;
        if ($isAdmin) {
            $allowImport = true;
        } elseif ($user->gugus_mutu_id) {
            $gm = \App\Models\GugusMutu::find($user->gugus_mutu_id);
            $allowImport = $gm ? (bool)$gm->allow_import : false;
        }

        return Inertia::render('Reports/Show', [
            'report' => $report,
            'userRole' => $user->roles->pluck('name')->first(),
            'allowImport' => $allowImport,
            'canEdit' => $canEdit,
            'projects' => Project::all(),
        ]);
    }

    public function submitPlan(Request $request, $id)
    {
        $user = Auth::user();
        $report = ReportSubmission::findOrFail($id);
        
        $isOwner = $report->user_id === $user->id;
        $isAdmin = $user->hasRole(['admin', 'super-admin', 'superadmin']);
        $isManagerOfGM = $user->hasRole('manager') && $report->user->gugus_mutu_id === $user->gugus_mutu_id;

        if (!$isOwner && !$isAdmin && !$isManagerOfGM) {
            abort(403);
        }

        $report->update(['approval_status' => 'Pending']);
        return back()->with('success', 'Perencanaan diajukan dan menunggu persetujuan.');
    }

    public function submitReport(Request $request, $id)
    {
        $user = Auth::user();
        $report = ReportSubmission::findOrFail($id);
        
        $isOwner = $report->user_id === $user->id;
        $isAdmin = $user->hasRole(['admin', 'super-admin', 'superadmin']);
        $isManagerOfGM = $user->hasRole('manager') && $report->user->gugus_mutu_id === $user->gugus_mutu_id;

        if (!$isOwner && !$isAdmin && !$isManagerOfGM) {
            abort(403);
        }

        $report->update(['approval_status' => 'Pending']);
        return back()->with('success', 'Pelaporan Kinerja diajukan dan menunggu persetujuan.');
    }

    public function pullFromSchedule(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        $report = ReportSubmission::findOrFail($id);
        $user = Auth::user();

        // Security check
        $isAdmin = $user->hasRole(['admin', 'super-admin', 'superadmin']);
        $isOwner = $report->user_id === $user->id;
        $isSameGM = ($user->gugus_mutu_id && $report->user->gugus_mutu_id === $user->gugus_mutu_id);

        if (!$isAdmin && !$isOwner && !$isSameGM) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        if (!$report->project_id) {
            $report->update(['project_id' => $request->project_id]);
        }

        $project = Project::with('tasks')->findOrFail($request->project_id);
        
        $count = 0;
        foreach ($project->tasks as $task) {
            if ($task->start_date) {
                $endDate = date('Y-m-d', strtotime($task->start_date . ' + ' . $task->duration_days . ' days'));
            } else {
                $endDate = null;
            }

            Activity::create([
                'report_submission_id' => $report->id,
                'nama_kegiatan_turunan' => $task->name,
                'rencana_start_date' => $task->start_date,
                'rencana_end_date' => $endDate,
                'deskripsi_kegiatan' => $project->name,
                'status_akhir' => 'Belum',
                'percent_complete' => $task->percent_complete,
                'duration_days' => $task->duration_days,
            ]);
            $count++;
        }

        return back()->with('success', "Berhasil menarik $count kegiatan dari jadwal proyek.");
    }

    public function downloadTemplateExcel(Request $request, $id)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
        ]);
        
        $project = Project::findOrFail($request->project_id);
        $fileName = 'Template_Laporan_' . str_replace(' ', '_', $project->name) . '.xlsx';

        return Excel::download(new ActivityExport($project->id), $fileName);
    }

    public function importExcel(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $report = ReportSubmission::findOrFail($id);
        $user = Auth::user();

        // Security check
        $isAdmin = $user->hasRole(['admin', 'super-admin', 'superadmin']);
        $isOwner = $report->user_id === $user->id;
        $isSameGM = ($user->gugus_mutu_id && $report->user->gugus_mutu_id === $user->gugus_mutu_id);

        if (!$isAdmin && !$isOwner && !$isSameGM) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        try {
            Excel::import(new ActivityImport($report->id), $request->file('file'));
            return back()->with('success', 'Data Excel berhasil diunggah dan diimpor!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengimpor: ' . $e->getMessage()]);
        }
    }

    public function updateActivity(Request $request, $id, $activityId)
    {
        $request->validate([
            'realisasi_start_date' => 'nullable|date',
            'realisasi_end_date' => 'nullable|date',
            'status_akhir' => 'nullable|string',
            'kendala' => 'nullable|string',
            'mitigasi' => 'nullable|string',
        ]);

        $activity = Activity::findOrFail($activityId);
        $activity->update([
            'realisasi_start_date' => $request->realisasi_start_date,
            'realisasi_end_date' => $request->realisasi_end_date,
            'status_akhir' => $request->status_akhir,
            'kendala' => $request->kendala,
            'mitigasi' => $request->mitigasi,
        ]);

        return back()->with('success', 'Realisasi dan Tindakan Mitigasi berhasil disimpan.');
    }
    public function export()
    {
        $user = Auth::user();
        $query = Activity::with(['reportSubmission.user.gugusMutu', 'reportSubmission.period', 'reportSubmission.project.gugusMutu']);

        if ($user->hasRole(['admin', 'super-admin'])) {
            // Admin sees all
        } elseif ($user->hasRole('manager')) {
            $query->whereHas('reportSubmission.user', function($q) use ($user) {
                $q->where('gugus_mutu_id', $user->gugus_mutu_id);
            });
        } else {
            $query->whereHas('reportSubmission', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $activities = $query->get();
        return \Excel::download(new \App\Exports\ReportsExport($activities), 'Daftar_Laporan.xlsx');
    }
}













