<?php

namespace App\Domain\Task\Listeners;

use App\Domain\Task\Events\TaskUpdated;
use Illuminate\Support\Facades\Log;

final class LogTaskUpdated
{
    public function handle(TaskUpdated $event): void
    {
        Log::info('Task updated', [
            'task_id' => $event->newTask->id,
            'user_id' => $event->newTask->userId,

            'old' => [
                'title' => $event->oldTask->title,
                'description' => $event->oldTask->description,
                'priority' => $event->oldTask->priority->value,
                'due_date' => $event->oldTask->dueDate?->format('Y-m-d H:i:s'),
            ],

            'new' => [
                'title' => $event->newTask->title,
                'description' => $event->newTask->description,
                'priority' => $event->newTask->priority->value,
                'due_date' => $event->newTask->dueDate?->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
