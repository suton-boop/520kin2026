<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\ProjectTaskObserver;

#[ObservedBy(ProjectTaskObserver::class)]
class ProjectTask extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(ProjectTask::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProjectTask::class, 'parent_id');
    }

    public function resources()
    {
        return $this->hasMany(TaskResource::class);
    }
}

