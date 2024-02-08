<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Task;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'name'];

    
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'project_tasks');
    }
    public function task($task_id, $project_id)
	{
	    return $this->belongsToMany(Task::class, 'project_tasks')
	                ->wherePivot('task_id', $task_id)
	                ->wherePivot('project_id', $project_id); 
	}
    public function project_tasks()
    {
        return $this->hasMany(ProjectTask::class, 'project_id');
    }
    public function project_task()
    {
        return $this->belongsTo(ProjectTask::class, 'id', 'project_id');
    }
}
