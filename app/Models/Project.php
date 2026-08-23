<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date', 'description', 'gugus_mutu_id'];

    public function reportSubmissions()
    {
        return $this->hasMany(ReportSubmission::class);
    }

    public function tasks()
    {
        return $this->hasMany(ProjectTask::class);
    }

    public function gugusMutu()
    {
        return $this->belongsTo(GugusMutu::class);
    }
}
