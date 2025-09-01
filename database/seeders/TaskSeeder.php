<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class TaskSeeder extends Seeder
{
    public function run()
    {
        $users = User::inRandomOrder()->limit(5)->get();
        $statuses = Task::getStatuses();

        $tasks = [];
        foreach (range(1, 20) as $index) {
            $task = Task::create([
                'title' => "Task Title #$index",
                'description' => "Description for task #$index",
                'status' => $statuses[array_rand($statuses)],
                'due_date' => '2025-09-20',
                'assigned_to' => $users->random()->id,
            ]);

            $tasks[] = $task;

            if (rand(0, 1)) {
                $dependencies = Task::where('id', '!=', $task->id)
                    ->inRandomOrder()
                    ->limit(rand(1, 3))
                    ->get();

                foreach ($dependencies as $dependency) {
                    $task->dependencies()->attach($dependency->id);
                }
            }
        }
    }
}
