<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Activity;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Activity::with(['reportSubmission.user.gugusMutu', 'reportSubmission.period']);

        if ($user->hasRole(['admin', 'super-admin'])) {
            $query->whereIn('approval_status', ['Pending', 'Approved', 'Rejected']);
        } elseif ($user->hasRole('manager')) {
            $query->whereHas('reportSubmission.user', function($q) use ($user) {
                $q->where('gugus_mutu_id', $user->gugus_mutu_id);
            });
            $query->whereIn('approval_status', ['Pending', 'Approved', 'Rejected']);
        } else {
            $query->where('id', 0);
        }

        $pending_approvals = $query->orderBy('created_at', 'desc')->paginate(15);

        return Inertia::render('Approvals/Index', [
            'pending_approvals' => $pending_approvals,
            'userRole' => $user->roles->pluck('name')->first()
        ]);
    }

    public function approve(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
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

        $activity->update($updates);

        return back()->with('success', 'Kegiatan berhasil Disahkan.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate(['reason' => 'required|string']);
        $activity = Activity::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasRole(['manager', 'admin', 'super-admin'])) abort(403);

        $activity->update([
            'approval_status' => 'Rejected',
            'reviewer_notes' => $request->reason,
        ]);

        return back()->with('success', 'Kegiatan berhasil ditolak.');
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

        Activity::whereIn('id', $request->submission_ids)->update($updates);

        return back()->with('success', 'Semua kegiatan yang dipilih berhasil disahkan.');
    }

    public function cancel(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $user = Auth::user();

        if (!$user->hasRole(['manager', 'admin', 'super-admin'])) abort(403);

        $updates = [
            'approval_status' => 'Pending',
        ];

        if ($user->hasRole(['admin', 'super-admin'])) {
            $updates['admin_id'] = null;
            $updates['admin_approved_at'] = null;
        } else {
            $updates['manager_id'] = null;
            $updates['manager_approved_at'] = null;
        }

        $activity->update($updates);

        return back()->with('success', 'Status persetujuan berhasil dibatalkan (kembali ke Pending).');
    }
}
