<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\ReportSubmission;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = ReportSubmission::with(['user.gugusMutu', 'period', 'activities']);

        if ($user->hasRole(['admin', 'super-admin'])) {
            $query->whereIn('approval_status', ['Pending', 'Approved', 'Rejected']);
        } elseif ($user->hasRole('manager')) {
            $query->whereHas('user', function($q) use ($user) {
                $q->where('gugus_mutu_id', $user->gugus_mutu_id);
            });
            $query->whereIn('approval_status', ['Pending', 'Approved', 'Rejected']);
        } else {
            $query->where('id', 0);
        }

        $pending_approvals = $query->orderBy('created_at', 'desc')->get();

        return Inertia::render('Approvals/Index', [
            'pending_approvals' => $pending_approvals,
            'userRole' => $user->roles->pluck('name')->first()
        ]);
    }

    public function approve(Request $request, $id)
    {
        $report = ReportSubmission::findOrFail($id);
        $user = Auth::user();
        if (!$user->hasRole(['manager', 'admin', 'super-admin'])) abort(403);

        $updates = [
            'approval_status' => 'Approved',
        ];

        if ($user->hasRole(['admin', 'super-admin'])) {
            $updates['admin_id'] = $user->id;
            $updates['admin_approved_at'] = now();
        } else {
            $updates['manager_id'] = $user->id;
            $updates['manager_approved_at'] = now();
        }

        $report->update($updates);

        return back()->with('success', 'Laporan/Rencana berhasil Disahkan.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        $report = ReportSubmission::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasRole(['manager', 'admin', 'super-admin'])) abort(403);

        $report->update([
            'approval_status' => 'Rejected',
            'reviewer_notes' => $request->reason,
        ]);

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }
    public function bulkApprove(Request $request)
    {
        $request->validate(['submission_ids' => 'required|array']);
        $user = Auth::user();

        if (!$user->hasRole(['manager', 'admin', 'super-admin'])) abort(403);

        $updates = [
            'approval_status' => 'Approved',
        ];

        if ($user->hasRole(['admin', 'super-admin'])) {
            $updates['admin_id'] = $user->id;
            $updates['admin_approved_at'] = now();
        } else {
            $updates['manager_id'] = $user->id;
            $updates['manager_approved_at'] = now();
        }

        ReportSubmission::whereIn('id', $request->submission_ids)->update($updates);

        return back()->with('success', 'Semua laporan yang dipilih berhasil disahkan.');
    }
}



