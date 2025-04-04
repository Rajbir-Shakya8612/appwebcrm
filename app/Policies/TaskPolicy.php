<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Check if user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        return $task->assignee_id === $user->id;
    }

    /**
     * Check if user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        return $task->assignee_id === $user->id;
    }

    /**
     * Check if user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->assignee_id === $user->id;
    }

    /**
     * Allow all users to create tasks.
     */
    public function create(User $user): bool
    {
        return true;
    }
    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
