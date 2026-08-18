<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectTask;
use App\Models\TaskDependency;
use Illuminate\Http\Request;

class ProjectTaskController extends Controller
{
    public function getGanttData(Project $project)
    {
        $tasks = $project->tasks()->orderBy('sort_order')->get()->map(function($task) {
            return [
                'id' => $task->id,
                'text' => $task->name,
                'start_date' => $task->start_date ? date('Y-m-d H:i:s', strtotime($task->start_date)) : null,
                'duration' => $task->duration_days,
                'parent' => $task->parent_id ?? 0,
                'progress' => $task->percent_complete / 100,
                'is_auto_scheduled' => $task->is_auto_scheduled,
                'effort_driven' => $task->effort_driven,
                'task_id_number' => $task->task_id_number,
                'wbs_code' => $task->wbs_code,
                'work_hours' => $task->work_hours,
                'actual_work_hours' => $task->actual_work_hours,
                'actual_cost' => $task->actual_cost,
            ];
        });

        $taskIds = $project->tasks()->pluck('id');
        $links = TaskDependency::whereIn('predecessor_task_id', $taskIds)
            ->orWhereIn('successor_task_id', $taskIds)
            ->get()->map(function($link) {
                // Map dependency_type to DHTMLX type (0: FS, 1: SS, 2: FF, 3: SF)
                $typeMap = ['FS' => '0', 'SS' => '1', 'FF' => '2', 'SF' => '3'];
                return [
                    'id' => $link->id,
                    'source' => $link->predecessor_task_id,
                    'target' => $link->successor_task_id,
                    'type' => $typeMap[$link->dependency_type] ?? '0',
                    'lag' => $link->lag_days,
                ];
            });

        return response()->json([
            'data' => $tasks,
            'links' => $links
        ]);
    }

    public function storeTask(Request $request, Project $project)
    {
        $task = new ProjectTask();
        $task->project_id = $project->id;
        $task->name = $request->text;
        $task->start_date = $request->start_date ? date('Y-m-d H:i:s', strtotime($request->start_date)) : null;
        $task->duration_days = $request->duration ?? 1;
        $task->parent_id = $request->parent == 0 ? null : $request->parent;
        $task->percent_complete = ($request->progress ?? 0) * 100;
        $task->save();

        return response()->json([
            'action' => 'inserted',
            'tid' => $task->id
        ]);
    }

    public function updateTask(Request $request, Project $project, ProjectTask $task)
    {
        $task->name = $request->text;
        $task->start_date = $request->start_date ? date('Y-m-d H:i:s', strtotime($request->start_date)) : null;
        $task->duration_days = $request->duration ?? $task->duration_days;
        $task->parent_id = $request->parent == 0 ? null : $request->parent;
        $task->percent_complete = ($request->progress ?? 0) * 100;
        
        // Also update other custom fields if provided
        if ($request->has('is_auto_scheduled')) $task->is_auto_scheduled = $request->is_auto_scheduled === 'true' || $request->is_auto_scheduled === true;
        if ($request->has('work_hours')) $task->work_hours = $request->work_hours;
        if ($request->has('actual_work_hours')) $task->actual_work_hours = $request->actual_work_hours;
        if ($request->has('actual_cost')) $task->actual_cost = $request->actual_cost;
        
        $task->save();

        return response()->json([
            'action' => 'updated'
        ]);
    }

    public function destroyTask(Project $project, ProjectTask $task)
    {
        $task->delete();
        return response()->json([
            'action' => 'deleted'
        ]);
    }

    public function storeLink(Request $request, Project $project)
    {
        $typeMap = ['0' => 'FS', '1' => 'SS', '2' => 'FF', '3' => 'SF'];
        
        $link = new TaskDependency();
        $link->predecessor_task_id = $request->source;
        $link->successor_task_id = $request->target;
        $link->dependency_type = $typeMap[$request->type] ?? 'FS';
        $link->lag_days = $request->lag ?? 0;
        $link->save();

        return response()->json([
            'action' => 'inserted',
            'tid' => $link->id
        ]);
    }

    public function updateLink(Request $request, Project $project, TaskDependency $link)
    {
        $typeMap = ['0' => 'FS', '1' => 'SS', '2' => 'FF', '3' => 'SF'];
        
        $link->predecessor_task_id = $request->source;
        $link->successor_task_id = $request->target;
        $link->dependency_type = $typeMap[$request->type] ?? 'FS';
        $link->lag_days = $request->lag ?? 0;
        $link->save();

        return response()->json([
            'action' => 'updated'
        ]);
    }

    public function destroyLink(Project $project, TaskDependency $link)
    {
        $link->delete();
        return response()->json([
            'action' => 'deleted'
        ]);
    }
}


