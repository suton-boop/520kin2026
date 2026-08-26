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

        // Helper to find a key that contains a specific string
        $findKey = function($search) use ($row) {
            foreach(array_keys($row) as $key) {
                if (str_contains($key, $search)) return $row[$key];
            }
            return null;
        };

        $wbs = $row["wbs"] ?? $row["kode"] ?? null;
        
        $taskName = $row["task_name_nama_kegiatan"] ?? $row["task_name"] ?? $row["name"] ?? $row["kegiatan"] ?? $findKey("nama_kegiatan") ?? $findKey("task_name") ?? null;
        
        $indicators = $row["indikator_kinerja"] ?? $row["indicators"] ?? $findKey("indikator") ?? null;
        $resourceNames = $row["resource_names"] ?? $row["peserta_sasaran"] ?? $row["peserta_sasa"] ?? $findKey("peserta") ?? null;
        
        $startStr = $row["bulan_rencana_mulai"] ?? $row["start"] ?? $findKey("rencana_mulai") ?? $findKey("rencana_m") ?? null;
        $finishStr = $row["bulan_rencana_selesai"] ?? $row["finish"] ?? $findKey("rencana_selesai") ?? $findKey("rencana_s") ?? null;
        
        $start = $this->transformDate($startStr);
        $finish = $this->transformDate($finishStr);
        
        $actualStart = $this->transformDate($row["actual_start"] ?? null);
        $actualFinish = $this->transformDate($row["actual_finish"] ?? null);
        $baselineStart = $this->transformDate($row["baseline_start"] ?? null);
        $baselineFinish = $this->transformDate($row["baseline_finish"] ?? null);
        
        $percentComplete = $this->transformPercent($row["percent_complete"] ?? $row["complete"] ?? 0);
        $duration = $this->transformDuration($row["duration"] ?? 1);
        $actualDuration = $this->transformDuration($row["actual_duration"] ?? 0);
        $remainingDuration = $this->transformDuration($row["remaining_duration"] ?? 0);
        
        $actualCost = $this->transformNumber($row["anggaran"] ?? $row["actual_cost"] ?? $findKey("anggaran") ?? 0);
        $actualWork = $this->transformNumber($row["actual_work"] ?? 0);
        
        if (empty($taskName)) {
            return null; // Skip empty rows
        }

        return new ProjectTask([
            "project_id" => $this->projectId,
            "wbs_code" => $wbs,
            "name" => $taskName,
            "indicators" => $indicators,
            "resource_names" => $resourceNames,
            
            "start_date" => $start,
            "finish_date" => $finish,
            "actual_start_date" => $actualStart,
            "actual_finish_date" => $actualFinish,
            "baseline_start_date" => $baselineStart,
            "baseline_finish_date" => $baselineFinish,
            
            "percent_complete" => $percentComplete,
            "duration_days" => $duration,
            "actual_duration_days" => $actualDuration,
            "remaining_duration_days" => $remainingDuration,
            
            "actual_cost" => $actualCost,
            "actual_work_hours" => $actualWork,
            
            "sort_order" => $this->order,
            "is_auto_scheduled" => true,
            "effort_driven" => false,
            "is_estimated" => false,
        ]);
    }
    
    private function transformDate($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format("Y-m-d H:i:s");
        }
        
        $idMonths = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $enMonths = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        $value = str_ireplace($idMonths, $enMonths, $value);
        
        // Remove spaces inside ranges if any (e.g. "1 Juli 2026")
        
        try {
            return Carbon::parse($value)->format("Y-m-d H:i:s");
        } catch (\Exception $e) {
            return null;
        }
    }

    private function transformPercent($value)
    {
        if (empty($value)) return 0;
        $val = str_replace("%", "", $value);
        return (float) $val;
    }

    private function transformDuration($value)
    {
        if (empty($value)) return 0;
        $val = preg_replace("/[^0-9.]/", "", $value);
        return (float) $val;
    }

    private function transformNumber($value)
    {
        if (empty($value)) return 0;
        $val = preg_replace("/[^0-9.]/", "", $value);
        return (float) $val;
    }
}
