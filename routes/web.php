<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dashboard\TasksController;
use App\Http\Controllers\Dashboard\ProjectsController;
use App\Http\Controllers\HomeController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/test', function () {
    return 123;
});
Route::get('/test1', function () {
    return 123456;
});
Auth::routes();

Route::get('/dashboard', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
	Route::get('/tasks/order', [TasksController::class, 'task_order'])->name('tasks_order');
	Route::post('/tasks/order_change', [TasksController::class, 'task_order_change'])->name('tasks.order_change');
	Route::get('/tasks/{tasks}/projects', [ProjectsController::class, 'create'])->name('dashboard.tasks.projects');
	Route::resource('tasks', TasksController::class);
	Route::get('/projects/order', [ProjectsController::class, 'project_order'])->name('projects_order');
	Route::post('/projects/order_change', [ProjectsController::class, 'project_order_change'])->name('projects.order_change');
	Route::get('/projects/tasks/{projectId}', [ProjectsController::class, 'project_task_order'])->name('projects.task.order');
	Route::get('/projects/tasks/{task}', [ProjectsController::class, 'tasks'])->name('projects.task');
	Route::post('/projects/tasks/{task}', [ProjectsController::class, 'store'])->name('projects.tasks.store');
	Route::get('/projects/task-projects/{task}', [ProjectsController::class, 'get_tasks'])->name('projects.get.task');
	Route::get('/projects/{project}/tasks', [ProjectsController::class, 'project_tasks'])->name('projects.task.manage');
	Route::post('/projects/redirect', [ProjectsController::class, 'redirect'])->name('projects.redirect');
	Route::resource('projects', ProjectsController::class);
});
