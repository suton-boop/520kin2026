<?php

namespace App\Imports;

use App\Models\Activity;
use App\Models\ReportSubmission;
use App\Models\GugusMutu;
use App\Models\Period;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProgramImport implements ToCollection, WithStartRow
{
    protected $period = null;
    protected $currentProgram;
    protected $gugusMutuId;
    protected $reportId;
    public $rowCount = 0;

    public function __construct($gugusMutuId = null, $reportId = null)
    {
        $this->gugusMutuId = $gugusMutuId;
        $this->reportId = $reportId;
        $this->period = Period::firstOrCreate(
            ['month_year' => '01-2026'],
            ['start_date' => '2026-01-01', 'end_date' => '2026-12-31']
        );
    }

    public function startRow(): int
    {
        return 2;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $kode = trim($row[0] ?? '');
            $kegiatan = $row[1] ?? '';
            $indikator = $row[2] ?? '';
            $hasil = $row[3] ?? '';
            $mekanisme = $row[4] ?? '';
            
            // JIKA BARIS JUDUL PROGRAM (A. atau D. atau E)
            if (preg_match('/^[A-Z](\.)?$/i', $kode) || preg_match('/^[A-Z]\./i', $kode) || preg_match('/^\d+$/', $kode)) {
                $this->currentProgram = $kegiatan;
                continue;
            }

            if (empty($kegiatan)) continue;

            // LOGIKA PEMINDAI OTOMATIS (Handle kolom bergeser)
            $anggaran = null;
            $bulan = null;
            $gm_name_raw = null;

            // Scan kolom 7 sampai 13 (G sampai M) untuk mencari data yang bergeser
            for ($i = 7; $i <= 13; $i++) {
                $val = trim($row[$i] ?? '');
                if (empty($val)) continue;

                // 1. Deteksi Anggaran (Ciri: Angka murni > 1000)
                // Filter out non-numeric characters for check to be sure, but strict is fine.
                $clean_val = preg_replace('/[^0-9]/', '', $val);
                if (is_numeric($clean_val) && (float)$clean_val > 1000 && $anggaran === null) {
                    // Kalau isinya 99% angka, dia ini anggaran
                    if (strlen($clean_val) > 4 && strlen($clean_val) >= strlen($val) - 2) {
                        $anggaran = $clean_val;
                    }
                }

                // 2. Deteksi Bulan (Ciri: Mengandung nama bulan)
                if ($this->detectMonth($val) && $bulan === null) {
                    $bulan = $val;
                }

                // 3. Deteksi Gugus Mutu (Ciri: Mengandung kata 'GM')
                if (stripos($val, 'GM') !== false && $gm_name_raw === null) {
                    $gm_name_raw = $val;
                }
            }

            // Fallback jika scannner gagal
            if (!$anggaran) $anggaran = $row[8] ?? $row[9] ?? '';
            if (!$bulan) $bulan = $row[9] ?? $row[10] ?? '';
            if (!$gm_name_raw) $gm_name_raw = $row[11] ?? '';

            $submission = null;
            if ($this->reportId) {
                // Pastikan target adalah sesuai yang ada di URL
                $submission = ReportSubmission::find($this->reportId);
            }

            if (!$submission) {
                $gm = null;
                if (!empty($gm_name_raw)) {
                    $clean_gm_name = preg_replace('/[^A-Za-z0-9]/', '', $gm_name_raw);
                    $allGMs = GugusMutu::all();
                    foreach ($allGMs as $g) {
                        $clean_db_name = preg_replace('/[^A-Za-z0-9]/', '', $g->name);
                        if (stripos($clean_db_name, $clean_gm_name) !== false || stripos($clean_gm_name, $clean_db_name) !== false) {
                            $gm = $g;
                            break;
                        }
                    }
                }
                if (!$gm && $this->gugusMutuId) $gm = GugusMutu::find($this->gugusMutuId);
                
                $userId = auth()->id();
                if ($gm) {
                    $operator = $gm->users()->whereHas('roles', function($q) {
                        $q->whereIn('name', ['user', 'staff']);
                    })->first();
                    $userId = $operator ? $operator->id : ($gm->users()->first()?->id ?? auth()->id());
                }

                $submission = ReportSubmission::firstOrCreate([
                    'user_id' => $userId,
                    'period_id' => $this->period->id,
                    'form_type' => 'plan',
                ], [
                    'approval_status' => 'Draft',
                ]);
            }

            if (!$submission) continue;

            // TRUNCATE STRING UNTUK MENCEGAH "DATA TOO LONG" DATABASE EXCEPTION!
            try {
                Activity::create([
                    'report_submission_id' => $submission->id,
                    'kode_pmo' => Str::limit($kode, 250, ''),
                    'nama_kegiatan_turunan' => $kegiatan, // Text type
                    'deskripsi_kegiatan' => $indikator,   // Text type
                    'hasil_kegiatan' => $hasil,           // Text type
                    'mekanisme_kegiatan' => Str::limit($mekanisme, 250, ''), // VARCHAR(255)
                    'peserta_sasaran' => $row[6] ?? '',   // Text type
                    'tempat_kegiatan' => Str::limit($row[7] ?? '', 250, ''), // VARCHAR(255)
                    'jumlah_target' => '1',
                    'kode_rrkl' => Str::limit($anggaran, 250, ''), // VARCHAR(255)
                    'rencana_start_date' => $this->parseDate($bulan),
                    'rencana_end_date' => $this->parseDate($bulan),
                    'nama_kegiatan_di_dipa' => $this->currentProgram,
                    'status_akhir' => 'Belum',
                ]);
                
                $this->rowCount++;
            } catch (\Exception $dbError) {
                // Jika 1 baris gagal karena database, skip ke baris berikutnya jangan batalkan semuanya
                Log::error("Gagal simpan baris activity Excel: " . $dbError->getMessage());
            }
        }
    }

    private function detectMonth($val)
    {
        $months = ['januari', 'februari', 'maret', 'april', 'mei', 'juni', 'juli', 'agustus', 'september', 'oktober', 'november', 'desember'];
        $lower = strtolower($val);
        foreach ($months as $m) {
            if (str_contains($lower, $m)) return true;
        }
        return false;
    }

    private function parseDate($val)
    {
        if (empty($val)) return null;
        $map = [
            'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
            'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
            'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12'
        ];
        $lower = strtolower($val);
        foreach ($map as $m => $num) {
            if (str_contains($lower, $m)) return date('Y') . "-$num-01";
        }
        return null;
    }
}
