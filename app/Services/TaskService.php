<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;

class TaskService
{

    public function index(User $user)
    {
        return Task::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();
    }

    public function store(User $user, array $data)
    {
        $task = new Task();
        $task->title = $data['title'];
        $task->description = $data['description'] ?? null;
        $task->status = 'pending';
        $task->user_id = $user->id;
        $task->save();

        return $task;
    }

    public function updateStatus(User $user, $taskId, $status)
    {
        $task = Task::where('user_id', $user->id)->find($taskId);

        if (!$task) return null;

        $task->status = $status;
        $task->save();

        return $task;
    }

    public function delete(User $user, $taskId)
    {
        $task = Task::where('user_id', $user->id)->find($taskId);

        if (!$task) return false;

        $task->delete();
        return true;
    }
}
