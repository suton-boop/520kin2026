<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function predecessor()
    {
        return $this->belongsTo(ProjectTask::class, 'predecessor_task_id');
    }

    public function successor()
    {
        return $this->belongsTo(ProjectTask::class, 'successor_task_id');
    }
}
