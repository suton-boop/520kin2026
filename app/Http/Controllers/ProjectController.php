<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProjectTaskImport;
use App\Models\GugusMutu;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
        public function index()
    {
        $projects = Project::with('gugusMutu')->orderBy('created_at', 'desc')->get();
        $gugusMutus = GugusMutu::orderBy('name')->get();
        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'gugusMutus' => $gugusMutus
        ]);
    }

    public function store(Request $request)
    {
                $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'description' => 'nullable|string',
            'gugus_mutu_id' => 'nullable|exists:gugus_mutus,id',
        ]);

        Project::create($validated);

        return redirect()->back()->with('success', 'Project created successfully');
    }

    public function show(Project $project)
    {
        return Inertia::render('Projects/Gantt', [
            'project' => $project
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'description' => 'nullable|string',
            'gugus_mutu_id' => 'nullable|exists:gugus_mutus,id',
        ]);

        $project->update($validated);

        return redirect()->back()->with('success', 'Project updated successfully');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully');
    }

    public function importTasks(Request $request, Project $project)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new ProjectTaskImport($project->id), $request->file('file'));
            return redirect()->back()->with('success', 'Data perencanaan berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengimpor: ' . $e->getMessage()]);
        }
    }
    public function export()
    {
        $user = Auth::user();
        $query = \App\Models\ProjectTask::with(['project.gugusMutu']);

        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            // Admin sees all
        } elseif ($user->hasRole('manager')) {
            $query->whereHas('project', function($q) use ($user) {
                $q->where('gugus_mutu_id', $user->gugus_mutu_id);
            });
        } else {
            $query->whereHas('project', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $tasks = $query->orderBy('project_id')->orderBy('sort_order')->get();
        return \Excel::download(new \App\Exports\ProjectsExport($tasks), 'Daftar_Perencanaan_Detail.xlsx');
    }
}


