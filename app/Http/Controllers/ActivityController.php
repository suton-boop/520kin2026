<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\ReportSubmission;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function store(Request $request, $reportId)
    {
        $report = ReportSubmission::findOrFail($reportId);
        
        $validated = $request->validate([
            'kode_pmo' => 'nullable|string|max:255',
            'kode_rrkl' => 'nullable|string|max:255',
            'jumlah_target' => 'nullable|string|max:255',
            'jumlah_capaian' => 'nullable|string|max:255',
            'resiko_isu' => 'nullable|string',
            'solusi' => 'nullable|string',
            'nama_kegiatan_turunan' => 'required|string',
            'deskripsi_kegiatan' => 'nullable|string',
            'hasil_kegiatan' => 'nullable|string',
            'rencana_start_date' => 'nullable|date',
            'rencana_end_date' => 'nullable|date',
            'realisasi_start_date' => 'nullable|date',
            'realisasi_end_date' => 'nullable|date',
            'status_akhir' => 'nullable|string',
        ]);

        $report->activities()->create($validated);

        return redirect()->back()->with('success', 'Aktivitas berhasil ditambahkan.');
    }

    public function update(Request $request, Activity $activity)
    {
        $validated = $request->validate([
            'kode_pmo' => 'nullable|string|max:255',
            'kode_rrkl' => 'nullable|string|max:255',
            'jumlah_target' => 'nullable|string|max:255',
            'jumlah_capaian' => 'nullable|string|max:255',
            'resiko_isu' => 'nullable|string',
            'solusi' => 'nullable|string',
            'kendala' => 'nullable|string',
            'mitigasi' => 'nullable|string',
            'nama_kegiatan_turunan' => 'nullable|string',
            'deskripsi_kegiatan' => 'nullable|string',
            'hasil_kegiatan' => 'nullable|string',
            'rencana_start_date' => 'nullable|date',
            'rencana_end_date' => 'nullable|date',
            'realisasi_start_date' => 'nullable|date',
            'realisasi_end_date' => 'nullable|date',
            'status_akhir' => 'nullable|string',
        ]);

        $activity->update($validated);

        return redirect()->back()->with('success', 'Aktivitas berhasil diperbarui.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->back()->with('success', 'Aktivitas berhasil dihapus.');
    }
}
