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
Auth::routes();

Route::get('/dashboard', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
	Route::get('/tasks/order', [TasksController::class, 'task_order'])->name('tasks_order');
	Route::post('/tasks/order_change', [TasksController::class, 'task_order_change'])->name('tasks.order_change');
	Route::resource('tasks', TasksController::class);
	Route::resource('projects', ProjectsController::class);
});
