<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'password' => 'password',
            'roles' => ['manager'],
        ]);

        User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => 'password',
            'roles' => ['user'],
        ]);
    }
}
