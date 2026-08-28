<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_submission_id', 'project_task_id',
        'kode_pmo',
        'kode_rrkl',
        'jumlah_target',
        'jumlah_capaian',
        'resiko_isu',
        'solusi',
        'nama_kegiatan_turunan',
        'deskripsi_kegiatan',
        'hasil_kegiatan',
        'nama_kegiatan_di_dipa',
        'rencana_start_date',
        'rencana_end_date',
        'mekanisme_kegiatan',
        'peserta_sasaran',
        'tempat_kegiatan',
        'rincian_ketersediaan_anggaran',
        'persiapan',
        'realisasi_start_date',
        'realisasi_end_date',
        'laporan',
                        'status_akhir',
        'kendala',
        'mitigasi',
        'percent_complete',
        'duration_days',
        'approval_status',
        'manager_id',
        'admin_id',
        'manager_approved_at',
        'admin_approved_at',
        'reviewer_notes',
    ];

    /**
     * Virtual attributes for frontend mapping
     */
    protected $appends = [
        'task_name',
        'description',
        'rkkl_code',
        'target_count',
        'target_unit',
        'start_date',
        'end_date'
    ];

    public function getTaskNameAttribute() { return $this->nama_kegiatan_turunan; }
    public function getDescriptionAttribute() { return $this->deskripsi_kegiatan; }
    public function getRkklCodeAttribute() { return $this->kode_rrkl; }
    public function getTargetCountAttribute() { return $this->jumlah_target; }
    public function getTargetUnitAttribute() { return $this->hasil_kegiatan ?: 'dokumen'; }
    public function getStartDateAttribute() { return $this->rencana_start_date; }
    public function getEndDateAttribute() { return $this->rencana_end_date; }

    public function projectTask()
    {
        return $this->belongsTo(ProjectTask::class, 'project_task_id');
    }

    public function reportSubmission()
    {
        return $this->belongsTo(ReportSubmission::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function budget()
    {
        return $this->belongsTo(Anggaran::class, 'kode_rrkl', 'kode');
    }
}

