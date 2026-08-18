<?php
$file = 'app/Http/Controllers/ReportController.php';
$content = file_get_contents($file);

$content = str_replace(
    "use App\Models\ReportSubmission;",
    "use App\Models\ReportSubmission;\nuse App\Models\Project;\nuse App\Models\Activity;",
    $content
);

$content = str_replace(
    "'canEdit' => \, // Kirim flag ini ke UI",
    "'canEdit' => \,\n            'projects' => Project::all(),",
    $content
);

$newMethod = <<<EOT
    public function pullFromSchedule(Request \, \)
    {
        \->validate([
            'project_id' => 'required|exists:projects,id',
        ]);

        \ = ReportSubmission::findOrFail(\);
        \ = Auth::user();

        // Security check
        \ = \->hasRole(['admin', 'super-admin', 'superadmin']);
        \ = \->user_id === \->id;
        \ = (\->gugus_mutu_id && \->user->gugus_mutu_id === \->gugus_mutu_id);

        if (!\ && !\ && !\) {
            abort(403, 'Anda tidak memiliki akses.');
        }

        if (!\->project_id) {
            \->update(['project_id' => \->project_id]);
        }

        \ = Project::with('tasks')->findOrFail(\->project_id);
        
        \ = 0;
        foreach (\->tasks as \) {
            if (\->start_date) {
                \ = date('Y-m-d', strtotime(\->start_date . ' + ' . \->duration_days . ' days'));
            } else {
                \ = null;
            }

            Activity::create([
                'report_submission_id' => \->id,
                'nama_kegiatan_turunan' => \->name,
                'rencana_start_date' => \->start_date,
                'rencana_end_date' => \,
                'deskripsi_kegiatan' => 'Ditarik otomatis dari Penjadwalan: ' . \->name,
                'status_akhir' => 'Belum',
            ]);
            \++;
        }

        return back()->with('success', "Berhasil menarik \ kegiatan dari jadwal proyek.");
    }
}
EOT;

$content = preg_replace('/public function submitReport\(Request \, \\)(.*?)    \}\n\}/s', "public function submitReport(Request \, \)\    }\n\n\", $content);

file_put_contents($file, $content);
echo "ReportController updated.\n";
