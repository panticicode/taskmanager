<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::whereNotNull('id')->delete();
        
        $users = [
            [
                'name' => 'Admin',
                'role' => true,
                'email' => 'admin@tasks.local',
                'password' => bcrypt('123123123'),
                'email_verified_at' => now()
            ]
        ];
        foreach ($users as $user) {
            User::create($user);
        }
    }
}
