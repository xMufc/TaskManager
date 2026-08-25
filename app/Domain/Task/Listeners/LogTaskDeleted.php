<?php

namespace App\Domain\Task\Listeners;

use App\Domain\Task\Events\TaskDeleted;
use Illuminate\Support\Facades\Log;

final class LogTaskDeleted
{
    public function handle(TaskDeleted $event): void
    {
        Log::info('Task deleted', [
            'task_id' => $event->task->id,
            'user_id' => $event->task->userId,
        ]);
    }
}