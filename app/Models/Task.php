<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProjectTask;
use App\Models\Project;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'priority'];
    
    public function headerimage($image)
    {
        if(file_exists($this->putHeaderImage($image)))
        {
            return $this->getHeaderImage($image);
        }
        return url('/') . '/images/homepage-1/' . $image;
    }
    public function sidebarimage($code, $image)
    {
        $folder = $this->uploads; 
        if(file_exists($this->putSidebarImage($image)))
        {
            return $this->getSidebarImage($image);
        }
        return url('/') . $this->uploads . $code . '/' . $image;
    }
    public function getImage($file = NULL){
        return asset('/images/pages/visas/main/' . $file);
    }
    public function getHeaderImage($file = NULL){
        if(file_exists($this->putHeaderImage($file)))
        {

            return asset('/images/pages/visas/header/' . $file);
        }
         return asset('/images/homepage-1/' . $file);
        
    }
    public function putImage($file = NULL){
        return public_path() . '/images/pages/visas/main/' . $file;
    }
    public function putPhoto($file = NULL){
        $folder = $this->uploads;
        return asset('/images/pages/visas/sidebar/' . $file);
    }
    public function putHeaderImage($file = NULL){
        $folder = $this->uploads;
        
        return public_path() . '/images/pages/visas/header/' . $file;
    }
    public function getSidebarImage($image = null)
    {
        $folder = $this->uploads;
        return asset('/images/pages/visas/sidebar/' . $image);
    }
    public function putSidebarImage($image = null)
    {
        $folder = $this->uploads;
        return public_path() . '/images/pages/visas/sidebar/' . $image;
    }
    
    public function project_tasks()
    {
        return $this->belongsToMany(Project::class, 'project_tasks', 'task_id', 'project_id');
    }
    public function project_task()
    {
        return $this->belongsTo(ProjectTask::class, 'id', 'task_id');
    }
    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'project_tasks', 'project_id', 'task_id');
    }
    public function project($task_id, $project_id)
    {
        return $this->belongsToMany(Project::class, 'project_tasks')
                    ->wherePivot('task_id', $task_id)
                    ->wherePivot('project_id', $project_id); 
    }
}
