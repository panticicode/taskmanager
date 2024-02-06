<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('role', true)->first();

        $tasks = [
            [
            	'user_id' => $user->id,
            	'name' => 'Presentation Preparation'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'Client Meeting Scheduling'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'Customer Email Responses'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'Project X Documentation Organization'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'New Feature Code Review'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'Quarterly Budget Planning'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'Performance Improvement Technologies Research'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'User Interface Design Review'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'Application Security Testing'
            ],
            [
            	'user_id' => $user->id,
            	'name' => 'Team Meeting Coordination'
            ]
        ];

        foreach($tasks as $task)
        {
        	Task::create($task);
        }
    }
}
