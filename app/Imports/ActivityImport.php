<?php

namespace App\Imports;

use App\Models\Activity;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;

class ActivityImport implements ToModel, WithHeadingRow
{
    protected $reportId;

    public function __construct($reportId)
    {
        $this->reportId = $reportId;
    }

    public function model(array $row)
    {
        // Heading mapping in Excel usually becomes lowercase snake_case
        // 'nama_kegiatan_jangan_diubah'
        // 'rencana_start_date_yyyy_mm_dd'
        // 'rencana_end_date_yyyy_mm_dd'
        // 'status_akhir_belum_proses_selesai_tunda'
        // 'deskripsi_realisasi'
        
        // However, WithHeadingRow transforms headers. Let's map dynamically by keys or assume standard names.
        // It's safer to use indices if headers change, but WithHeadingRow uses associative array.
        // Let's use array_keys and find matches.
        
        $nama = '';
        $start = null;
        $end = null;
        $status = 'Belum';
        $deskripsi = '';

        foreach ($row as $key => $value) {
            if (str_contains($key, 'nama_kegiatan')) $nama = $value;
            if (str_contains($key, 'rencana_start')) $start = $this->transformDate($value);
            if (str_contains($key, 'rencana_end')) $end = $this->transformDate($value);
            if (str_contains($key, 'status_akhir')) $status = $value ?: 'Belum';
            if (str_contains($key, 'deskripsi')) $deskripsi = $value;
        }

        if (empty($nama)) return null;

        return new Activity([
            'report_submission_id' => $this->reportId,
            'nama_kegiatan_turunan' => $nama,
            'rencana_start_date' => $start,
            'rencana_end_date' => $end,
            'status_akhir' => $status,
            'deskripsi_kegiatan' => $deskripsi,
        ]);
    }
    
    private function transformDate($value)
    {
        if (empty($value)) return null;
        if (is_numeric($value)) {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        }
        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }
}