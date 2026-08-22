<?php

namespace App\Domain\Task\Listeners;

use App\Domain\Task\Events\TaskStatusChanged;
use Illuminate\Support\Facades\Log;

final class LogTaskStatusChanged
{
    public function handle(TaskStatusChanged $event): void
    {
        Log::info('Task status changed', [
            'task_id' => $event->task->id,
            'user_id' => $event->task->userId,
            'from' => $event->previousStatus->value,
            'to' => $event->task->status->value,
        ]);
    }
}