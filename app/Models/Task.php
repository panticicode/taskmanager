<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'priority'];
    
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
}
