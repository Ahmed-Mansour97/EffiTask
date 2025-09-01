<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    /**
     * Determine if the user can create a task.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function create(User $user)
    {
        return $user->isManager();
    }

    /**
     * Determine if the user can update the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function update(User $user, Task $task)
    {
        return $user->isManager();
    }

    /**
     * Determine if the user can delete the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function delete(User $user, Task $task)
    {
        return $user->isManager();
    }

    /**
     * Determine if the user can view the task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function show(User $user, Task $task)
    {
        return $user->id === $task->assigned_to || $user->isManager();
    }

    /**
     * Determine if the user can change the status of a task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function changeStatus(User $user, Task $task)
    {
        return $user->id === $task->assigned_to || $user->isManager();
    }

    /**
     * Determine if the user can assign a task.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return bool
     */
    public function assign(User $user, Task $task)
    {
        return $user->isManager();
    }
}
