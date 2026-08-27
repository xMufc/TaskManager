<?php

namespace App\Domain\Task\Listeners;

use App\Domain\Task\Events\TaskCreated;
use Illuminate\Support\Facades\Log;

final class LogTaskCreated
{
    public function handle(TaskCreated $event): void
    {
        Log::info('Task created', [
            'task_id' => $event->task->id,
            'user_id' => $event->task->userId,
            'title' => $event->task->title,
        ]);
    }
}
