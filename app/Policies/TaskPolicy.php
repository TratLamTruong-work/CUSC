<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

/**
 * [BỔ SUNG] Phân quyền Admin/User cho task.
 * Admin: thao tác mọi task. User: chỉ task của mình; task người khác -> 404 (không lộ sự tồn tại).
 */
class TaskPolicy
{
    public function view(User $user, Task $task): Response
    {
        return $this->canManage($user, $task);
    }

    public function update(User $user, Task $task): Response
    {
        return $this->canManage($user, $task);
    }

    public function delete(User $user, Task $task): Response
    {
        return $this->canManage($user, $task);
    }

    private function canManage(User $user, Task $task): Response
    {
        if ($user->isAdmin() || (int) $task->user_id === (int) $user->id) {
            return Response::allow();
        }

        return Response::denyAsNotFound();
    }
}
