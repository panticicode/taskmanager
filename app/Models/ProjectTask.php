<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;

class ProjectTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = ['user_id', 'task_id', 'project_id', 'deleted_at'];

    public function task()
    {
    	return $this->belongsTo(Task::class, 'task_id', 'id');
    }
    public function tasks()
    {
    	return $this->hasMany(Task::class, 'id', 'task_id');
    }
    public function project()
    {
    	return $this->belongsTo(Project::class, 'project_id', 'id');
    }
    public function projects()
    {
    	return $this->hasMany(Project::class, 'id', 'project_id');
    }
}
