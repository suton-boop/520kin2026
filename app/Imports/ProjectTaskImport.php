<?php

namespace App\Imports;

use App\Models\ProjectTask;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ProjectTaskImport implements ToModel, WithHeadingRow
{
    protected $projectId;
    protected $order = 0;

    public function __construct($projectId)
    {
        $this->projectId = $projectId;
    }

    public function model(array $row)
    {
        $this->order++;

        // Map columns based on PDF headers
        // Indicators, ID, Mode, WBS, Task Name, Start, Finish, Actual Start, Actual Finish
        // Baseline Start, Baseline Finish, % Complete, Duration, Actual Duration, Remaining Duration
        // Actual Cost, Actual Work, Resource Names

        $wbs = $row['wbs'] ?? null;
        $taskName = $row['task_name'] ?? ($row['name'] ?? null);
        $indicators = $row['indicators'] ?? null;
        $resourceNames = $row['resource_names'] ?? null;
        
        $start = $this->transformDate($row['start'] ?? null);
        $finish = $this->transformDate($row['finish'] ?? null);
        $actualStart = $this->transformDate($row['actual_start'] ?? null);
        $actualFinish = $this->transformDate($row['actual_finish'] ?? null);
        $baselineStart = $this->transformDate($row['baseline_start'] ?? null);
        $baselineFinish = $this->transformDate($row['baseline_finish'] ?? null);
        
        $percentComplete = $this->transformPercent($row['percent_complete'] ?? ($row['complete'] ?? 0));
        $duration = $this->transformDuration($row['duration'] ?? 1);
        $actualDuration = $this->transformDuration($row['actual_duration'] ?? 0);
        $remainingDuration = $this->transformDuration($row['remaining_duration'] ?? 0);
        
        $actualCost = $this->transformNumber($row['actual_cost'] ?? 0);
        $actualWork = $this->transformNumber($row['actual_work'] ?? 0);
        
        if (empty($taskName)) {
            return null; // Skip empty rows
        }

        return new ProjectTask([
            'project_id' => $this->projectId,
            'wbs_code' => $wbs,
            'name' => $taskName,
            'indicators' => $indicators,
            'resource_names' => $resourceNames,
            
            'start_date' => $start,
            'finish_date' => $finish,
            'actual_start_date' => $actualStart,
            'actual_finish_date' => $actualFinish,
            'baseline_start_date' => $baselineStart,
            'baseline_finish_date' => $baselineFinish,
            
            'percent_complete' => $percentComplete,
            'duration_days' => $duration,
            'actual_duration_days' => $actualDuration,
            'remaining_duration_days' => $remainingDuration,
            
            'actual_cost' => $actualCost,
            'actual_work_hours' => $actualWork,
            
            'sort_order' => $this->order,
            'is_auto_scheduled' => true,
            'effort_driven' => false,
            'is_estimated' => false,
        ]);
    }
    
    private function transformDate($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d H:i:s');
        }
        try {
            return Carbon::parse($value)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function transformPercent($value)
    {
        if (empty($value)) return 0;
        $val = str_replace('%', '', $value);
        return (float) $val;
    }

    private function transformDuration($value)
    {
        if (empty($value)) return 0;
        // e.g. "230d", "230 days"
        $val = preg_replace('/[^0-9.]/', '', $value);
        return (float) $val;
    }

    private function transformNumber($value)
    {
        if (empty($value)) return 0;
        // e.g. "Rp0,00", "232h"
        $val = preg_replace('/[^0-9.]/', '', $value);
        return (float) $val;
    }
}
