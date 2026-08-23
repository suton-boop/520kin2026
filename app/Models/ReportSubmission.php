<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'period_id', 'form_type', 'approval_status', 'project_id'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function activities()
    {
        return $this->hasMany(Activity::class);
    }
}


