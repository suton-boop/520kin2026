<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GugusMutu extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'allow_import'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function reportSubmissions()
    {
        return $this->hasMany(ReportSubmission::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
