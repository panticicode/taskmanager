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
}
