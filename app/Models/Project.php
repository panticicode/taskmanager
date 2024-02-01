<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = ['task_id', 'name'];

    public function tasks()
    {
    	return $this->belongsTo('App\Models\Task', 'id', 'task_id');
    }
}
